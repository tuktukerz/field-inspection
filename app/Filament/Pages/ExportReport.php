<?php

namespace App\Filament\Pages;

use App\Models\Visit;
use App\Models\Tower;
use App\Models\User;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use App\Exports\VisitsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $title = 'Laporan Data Inspeksi Lapangan';
    protected string $view = 'filament.pages.export-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_range' => 'custom',
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filter Laporan')
                    ->schema([
                        Radio::make('date_range')
                            ->label('Pilih Rentang Waktu')
                            ->options([
                                'this_month' => 'Bulan ini',
                                'last_month' => 'Bulan kemarin',
                                'this_year' => 'Tahun ini',
                                'last_year' => 'Tahun kemarin',
                                'custom' => 'Custom',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                switch ($state) {
                                    case 'this_month':
                                        $set('start_date', now()->startOfMonth()->format('Y-m-d'));
                                        $set('end_date', now()->endOfMonth()->format('Y-m-d'));
                                        break;
                                    case 'last_month':
                                        $set('start_date', now()->subMonth()->startOfMonth()->format('Y-m-d'));
                                        $set('end_date', now()->subMonth()->endOfMonth()->format('Y-m-d'));
                                        break;
                                    case 'this_year':
                                        $set('start_date', now()->startOfYear()->format('Y-m-d'));
                                        $set('end_date', now()->endOfYear()->format('Y-m-d'));
                                        break;
                                    case 'last_year':
                                        $set('start_date', now()->subYear()->startOfYear()->format('Y-m-d'));
                                        $set('end_date', now()->subYear()->endOfYear()->format('Y-m-d'));
                                        break;
                                }
                            }),
                        DatePicker::make('start_date')
                            ->label('Dari')
                            ->required()
                            ->visible(fn ($get) => $get('date_range') === 'custom')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('end_date')
                            ->label('Sampai')
                            ->required()
                            ->visible(fn ($get) => $get('date_range') === 'custom')
                            ->default(now()),
                        Select::make('tower_id')
                            ->label('Tower ID')
                            ->options(Tower::all()->pluck('tower_id', 'id'))
                            ->searchable()
                            ->placeholder('Semua Tower'),
                        Select::make('user_id')
                            ->label('Pemeriksa (Visited By)')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Semua Pemeriksa'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn() => $this->downloadPdf()),
            Action::make('downloadExcel')
                ->label('Unduh XLS')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action(fn() => $this->downloadExcel()),
        ];
    }

    public function downloadPdf()
    {
        $towerId = $this->data['tower_id'] ?? null;
        $userId = $this->data['user_id'] ?? null;

        if (!$startDate || !$endDate) {
            \Filament\Notifications\Notification::make()
                ->title('Pilih rentang tanggal')
                ->danger()
                ->send();
            return;
        }

        $query = Visit::with(['images', 'creator', 'tower'])
            ->whereBetween('inspection_date', [$startDate, $endDate]);

        if ($towerId) {
            $query->where('tower_id', $towerId);
        }

        if ($userId) {
            $query->where('created_by', $userId);
        }

        $visits = $query->orderBy('tower_id', 'asc')
            ->orderBy('inspection_date', 'asc')
            ->get();

        if ($visits->isEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Data tidak ditemukan')
                ->body('Tidak ada data ditemukan untuk rentang tanggal ini.')
                ->warning()
                ->send();
            return;
        }

        $dateObj = Carbon::parse($startDate);
        $triwulanNum = ceil($dateObj->month / 3);
        $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
        $quarterLabel = 'Triwulan ' . ($romans[$triwulanNum] ?? $triwulanNum);
        $tahun = $dateObj->year;

        $pdf = Pdf::loadView('reports.telecom-tower', [
            'inspections' => $visits,
            'startDate' => $dateObj->isoFormat('D MMMM YYYY'),
            'endDate' => Carbon::parse($endDate)->isoFormat('D MMMM YYYY'),
            'triwulan' => $triwulanNum,
            'tahun' => $tahun,
            'quarter' => $quarterLabel,
            'year' => $tahun,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Laporan_Menara_Telekomunikasi_{$startDate}_{$endDate}.pdf");
    }

    public function downloadExcel()
    {
        $towerId = $this->data['tower_id'] ?? null;
        $userId = $this->data['user_id'] ?? null;

        if (!$startDate || !$endDate) {
            \Filament\Notifications\Notification::make()
                ->title('Pilih rentang tanggal')
                ->danger()
                ->send();
            return;
        }

        return Excel::download(
            new VisitsExport($startDate, $endDate, $towerId, $userId),
            "Laporan_Menara_Telekomunikasi_{$startDate}_{$endDate}.xlsx"
        );
    }

    private function getQuarterLabel($date)
    {
        $month = Carbon::parse($date)->month;
        if ($month <= 3) return 'Triwulan I';
        if ($month <= 6) return 'Triwulan II';
        if ($month <= 9) return 'Triwulan III';
        return 'Triwulan IV';
    }
}
