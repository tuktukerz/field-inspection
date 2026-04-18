<x-filament-widgets::widget>
    <div
        style="
            position: relative;
            background: linear-gradient(135deg, {{ $accent }}15 0%, {{ $accent }}05 50%, transparent 100%);
            border: 1px solid {{ $accent }}25;
            border-radius: 0.875rem;
            padding: 1.25rem 1.5rem;
            overflow: hidden;
        "
    >
        <div style="display: flex; align-items: center; gap: 1.125rem;">
            <div
                style="
                    width: 56px;
                    height: 56px;
                    border-radius: 0.75rem;
                    background: linear-gradient(135deg, {{ $accent }} 0%, {{ $accent }}cc 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 8px 16px -4px {{ $accent }}55;
                    flex-shrink: 0;
                "
            >
                <x-filament::icon
                    :icon="$icon"
                    style="width: 28px; height: 28px; color: #ffffff;"
                />
            </div>

            <div style="flex: 1; min-width: 0;">
                <h2
                    style="
                        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
                        font-size: 1.25rem;
                        font-weight: 700;
                        letter-spacing: -0.02em;
                        margin: 0 0 0.125rem 0;
                        line-height: 1.3;
                    "
                >
                    {{ $greeting }}, {{ $name }} 👋
                </h2>
                <p
                    style="
                        font-size: 0.8125rem;
                        color: rgb(100 116 139);
                        margin: 0;
                    "
                >
                    {{ $date }}
                </p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
