<style>
    .thailand-address-autocomplete .autoComplete_wrapper {
        display: block;
        width: 100% !important;
        position: relative;
    }

    /* Public: rounded search-style field + dropdown */
    .thailand-address-autocomplete--public .form-group-custom {
        position: relative;
        overflow: visible;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > input {
        width: 100% !important;
        height: 46px;
        border: 1px solid #CBD5E1 !important;
        border-radius: 10px !important;
        padding: 10px 14px !important;
        font-size: 14px !important;
        color: var(--oc-text-main, #0F172A) !important;
        background-color: #FFFFFF !important;
        background-image: none !important;
        box-sizing: border-box;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > input::placeholder {
        color: #94A3B8 !important;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > input:focus {
        border-color: #2F53B4 !important;
        box-shadow: 0 0 0 3.5px rgba(47, 83, 180, 0.12) !important;
        outline: none;
        color: var(--oc-text-main, #0F172A) !important;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul {
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
        z-index: 2000 !important;
        margin-top: 6px !important;
        padding: 6px !important;
        background: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 4px 6px -2px rgba(15, 23, 42, 0.05) !important;
        max-height: 240px !important;
        overflow-y: auto !important;
        list-style: none !important;
        font-family: 'IBM Plex Sans Thai', 'Noto Sans Thai', sans-serif !important;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul::-webkit-scrollbar {
        width: 6px;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 6px;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 6px;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul > li {
        padding: 10px 14px !important;
        font-size: 13.5px !important;
        color: #1E293B !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        margin-bottom: 2px !important;
        line-height: 1.4 !important;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul > li:last-child {
        margin-bottom: 0 !important;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul > li mark {
        background-color: transparent !important;
        color: #2F53B4 !important;
        font-weight: 700 !important;
        padding: 0 !important;
    }

    .thailand-address-autocomplete--public .autoComplete_wrapper > ul > li:hover,
    .thailand-address-autocomplete--public .autoComplete_wrapper > ul > li[aria-selected="true"] {
        background-color: #F1F5F9 !important;
        color: #2F53B4 !important;
    }

    /* Office: keep Bootstrap form-control appearance */
    .thailand-address-autocomplete--office .autoComplete_wrapper > input {
        display: block;
        width: 100% !important;
        max-width: 100%;
        height: auto !important;
        margin: 0 !important;
        padding: 0.5rem 0.9rem !important;
        box-sizing: border-box !important;
        font-size: var(--vz-font-base) !important;
        font-weight: var(--vz-font-weight-normal);
        line-height: 1.5;
        text-overflow: unset;
        color: var(--vz-body-color) !important;
        outline: 0 !important;
        border-radius: var(--vz-border-radius) !important;
        border: var(--vz-border-width) solid var(--vz-input-border-custom) !important;
        background-image: none !important;
        background-size: auto !important;
        background-position: unset !important;
        background-repeat: no-repeat !important;
        background-color: var(--vz-input-bg-custom) !important;
        box-shadow: none !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > input.form-control-sm {
        min-height: calc(1.5em + 0.5rem + calc(var(--vz-border-width) * 2));
        padding: 0.25rem 0.5rem !important;
        font-size: calc(var(--vz-font-base) * 0.875) !important;
        border-radius: var(--vz-border-radius-sm) !important;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > input::placeholder {
        padding: 0 !important;
        color: var(--vz-secondary-color) !important;
        font-size: inherit !important;
        opacity: 1;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > input:hover,
    .thailand-address-autocomplete--office .autoComplete_wrapper > input:focus {
        color: var(--vz-body-color) !important;
        background-color: var(--vz-input-bg-custom) !important;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > input:focus {
        border-color: var(--vz-primary-border-subtle) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--vz-primary-rgb), 0.25) !important;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > input.is-invalid,
    .thailand-address-autocomplete--office .autoComplete_wrapper > input.is-invalid:focus {
        border-color: var(--vz-form-invalid-border-color, #dc3545) !important;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > ul {
        left: 0;
        right: 0;
        width: 100%;
        z-index: 1050;
        margin: 0.25rem 0 0 0;
        padding: 0;
        max-height: 160px;
        overflow: auto;
        list-style: none;
        border-radius: var(--vz-border-radius, 0.25rem);
        border: var(--vz-border-width, 1px) solid var(--vz-border-color);
        background-color: var(--vz-secondary-bg, #fff);
        box-shadow: 0 5px 10px rgba(30, 32, 37, 0.12);
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > ul > li {
        margin: 0;
        padding: 0.35rem 0.9rem;
        font-size: var(--vz-font-base);
        color: var(--vz-body-color);
        border-radius: 0;
        background-color: var(--vz-secondary-bg, #fff);
        cursor: pointer;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > ul > li mark {
        background-color: transparent;
        color: var(--vz-danger);
        font-weight: var(--vz-font-weight-semibold, 600);
        padding: 0;
    }

    .thailand-address-autocomplete--office .autoComplete_wrapper > ul > li:hover,
    .thailand-address-autocomplete--office .autoComplete_wrapper > ul > li[aria-selected="true"] {
        color: var(--vz-body-color);
        background-color: var(--vz-tertiary-bg, #f3f6f9);
    }
</style>
