<style>
    /* Card Main Design System */
    .emp-card-main {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
        border: 1px solid #e2e8f0;
        padding: 24px;
        margin-bottom: 24px;
    }

    .document-form .card {
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .section-header-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .section-header-title i {
        color: #2F53B4;
        font-size: 1.25rem;
    }

    /* Labels & Form Control */
    .document-form .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }

    .document-form .form-text {
        font-size: 0.775rem;
        color: #64748b;
    }

    .document-form .form-control:not([type="file"]),
    .document-form .form-select,
    .document-form .dropdown-combo-input {
        height: 42px !important;
        padding: 0 14px !important;
        font-size: 0.875rem !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
        box-sizing: border-box !important;
    }

    .document-form .form-control[type="file"] {
        height: 42px !important;
        padding: 0 !important;
        font-size: 0.875rem !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        line-height: 42px !important;
    }
    .document-form .form-control[type="file"]::-webkit-file-upload-button {
        height: 42px !important;
        margin: 0 10px 0 0 !important;
        padding: 0 16px !important;
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: none !important;
        border-right: 1px solid #cbd5e1 !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
    }
    .document-form .form-control[type="file"]::file-selector-button {
        height: 42px !important;
        margin: 0 10px 0 0 !important;
        padding: 0 16px !important;
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: none !important;
        border-right: 1px solid #cbd5e1 !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
    }

    .document-form textarea.form-control {
        height: auto !important;
        padding: 10px 14px !important;
    }

    .document-form .form-control:focus,
    .document-form .form-select:focus,
    .document-form .dropdown-combo-input:focus {
        border-color: #2F53B4 !important;
        box-shadow: 0 0 0 3.5px rgba(47, 83, 180, 0.14) !important;
        outline: none !important;
    }

    .document-form .form-control::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }

    /* Date Icon Wrapper */
    .birthday-icon-wrapper {
        position: relative;
        width: 100%;
    }

    .birthday-icon-wrapper .birthday-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #2F53B4;
        font-size: 1.15rem;
        pointer-events: none;
        z-index: 5;
    }

    .birthday-icon-wrapper input.form-control {
        padding-right: 38px !important;
    }

    /* Custom Dropdown Combo Component */
    .custom-dropdown-combo {
        position: relative !important;
        width: 100% !important;
        height: 42px !important;
    }

    .custom-dropdown-combo .dropdown-combo-input {
        width: 100% !important;
        height: 42px !important;
        padding-right: 36px !important;
        cursor: pointer !important;
        background-color: #FFFFFF !important;
        text-align: left !important;
        box-sizing: border-box !important;
    }

    .custom-dropdown-combo .custom-fp-combo-arrow {
        position: absolute !important;
        right: 14px !important;
        top: 50% !important;
        margin-top: -3px !important;
        width: 0 !important;
        height: 0 !important;
        border-color: #2F53B4 transparent transparent transparent !important;
        border-style: solid !important;
        border-width: 6px 5px 0 5px !important;
        pointer-events: none !important;
        transition: transform 0.2s ease !important;
    }

    .custom-dropdown-combo.active .custom-fp-combo-arrow {
        transform: rotate(180deg) !important;
    }

    .custom-dropdown-popup {
        position: absolute !important;
        top: calc(100% + 4px) !important;
        left: 0 !important;
        right: 0 !important;
        background: #FFFFFF !important;
        border: 1px solid #CBD5E1 !important;
        border-radius: 10px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        z-index: 1050 !important;
        max-height: 170px !important;
        overflow-y: scroll !important;
        display: none;
        padding: 6px 0 !important;
        text-align: left !important;
        scrollbar-width: thin !important;
        scrollbar-color: #2F53B4 #E2E8F0 !important;
    }

    .custom-dropdown-popup::-webkit-scrollbar {
        width: 8px !important;
        height: 8px !important;
        display: block !important;
    }

    .custom-dropdown-popup::-webkit-scrollbar-track {
        background: #E2E8F0 !important;
        border-radius: 6px !important;
        margin: 4px 2px !important;
    }

    .custom-dropdown-popup::-webkit-scrollbar-thumb {
        background: #2F53B4 !important;
        border-radius: 6px !important;
        min-height: 30px !important;
    }

    .custom-dropdown-item {
        padding: 9px 16px !important;
        font-size: 0.875rem !important;
        color: #1E293B !important;
        cursor: pointer !important;
        transition: background-color 0.15s ease !important;
        text-align: left !important;
    }

    .custom-dropdown-item:hover {
        background-color: #EFF6FF !important;
        color: #2F53B4 !important;
    }

    .custom-dropdown-item.selected {
        background-color: #DBEAFE !important;
        color: #2F53B4 !important;
        font-weight: 600 !important;
    }

    /* Custom Flatpickr Header Controls CSS */
    .flatpickr-current-month {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        padding: 4px 0 !important;
        width: 100% !important;
    }

    .custom-fp-combo {
        position: relative !important;
        display: inline-block !important;
    }

    .custom-fp-combo.day-combo {
        width: 55px !important;
    }

    .custom-fp-combo.month-combo {
        width: 110px !important;
    }

    .custom-fp-combo.year-combo {
        width: 75px !important;
    }

    .custom-fp-combo-input {
        width: 100% !important;
        height: 32px !important;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 0 18px 0 8px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        text-align: center !important;
        box-sizing: border-box !important;
        box-shadow: none !important;
        cursor: pointer !important;
    }

    .custom-fp-combo-input:focus {
        border-color: #2F53B4 !important;
        outline: none !important;
    }

    .custom-fp-combo-arrow {
        position: absolute !important;
        right: 6px !important;
        top: 50% !important;
        margin-top: -2px !important;
        width: 0 !important;
        height: 0 !important;
        border-color: #475569 transparent transparent transparent !important;
        border-style: solid !important;
        border-width: 5px 4px 0 4px !important;
        pointer-events: none !important;
    }

    .custom-fp-dropdown-popup {
        position: absolute !important;
        top: calc(100% + 2px) !important;
        left: 0 !important;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
        z-index: 1060 !important;
        max-height: 160px !important;
        overflow-y: auto !important;
        display: none;
        padding: 4px 0 !important;
    }

    .day-dropdown-popup {
        width: 60px !important;
    }

    .month-dropdown-popup {
        width: 120px !important;
    }

    .year-dropdown-popup {
        width: 80px !important;
    }

    .custom-fp-combo-item {
        padding: 6px 10px !important;
        font-size: 0.8rem !important;
        color: #1e293b !important;
        cursor: pointer !important;
        text-align: center !important;
        transition: background-color 0.15s ease !important;
    }

    .custom-fp-combo-item:hover {
        background-color: #EFF6FF !important;
        color: #2F53B4 !important;
    }

    .custom-fp-combo-item.selected {
        background-color: #DBEAFE !important;
        color: #2F53B4 !important;
        font-weight: 700 !important;
    }

    /* Buttons */
    .btn-add-emp {
        background: linear-gradient(135deg, #2F53B4 0%, #1E3A8A 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 10px;
        padding: 10px 24px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(47, 83, 180, 0.28);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        cursor: pointer;
        height: 42px;
    }

    .btn-add-emp:hover {
        background: linear-gradient(135deg, #254394 0%, #172E6E 100%);
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(47, 83, 180, 0.38);
    }

    .btn-sec-emp {
        background-color: #ffffff;
        color: #2F53B4 !important;
        border: 1.5px solid #2F53B4;
        border-radius: 10px;
        padding: 10px 22px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 2px 6px rgba(47, 83, 180, 0.08);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        height: 42px;
    }

    .btn-sec-emp:hover {
        background-color: #EFF6FF;
        color: #1E3A8A !important;
        border-color: #1E3A8A;
        transform: translateY(-2px);
    }

    /* Sticky Bottom Actionbar */
    .doc-actionbar {
        position: sticky;
        bottom: 12px;
        z-index: 100;
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        padding: 16px 24px;
        margin-top: 1.5rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    /* Table Line Items */
    #doc-lines-table thead tr {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    #doc-lines-table thead th {
        color: #475569 !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.03em !important;
        padding: 14px 10px !important;
        white-space: nowrap;
    }

    .doc-totals-summary {
        min-width: 380px;
        max-width: 520px;
        padding-top: 0.15rem;
    }

    .doc-totals-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 0.35rem;
    }

    .doc-totals-row.border-bottom,
    .doc-totals-row.border-secondary-subtle {
        border-color: #e2e8f0 !important;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .doc-totals-grand {
        border-bottom: 3px double #cbd5e1 !important;
        padding-bottom: 0.5rem;
    }

    .doc-totals-label {
        flex: 1;
        text-align: right;
        font-size: 13px;
        line-height: 1.4;
        color: #475569;
        font-weight: 600;
    }

    .doc-totals-value {
        width: 185px;
        min-width: 185px;
        flex-shrink: 0;
    }

    .doc-totals-value .form-control,
    .doc-totals-input {
        font-size: 13px !important;
        padding: 0.4rem 0.55rem !important;
        line-height: 1.4 !important;
        font-variant-numeric: tabular-nums !important;
        font-weight: 600 !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }

    .doc-line-detail {
        font-size: 12px;
        line-height: 1.45;
        min-height: 52px;
        padding: 0.4rem 0.5rem;
    }

    /* Table Styles (Matching Lead/Opportunity design) */
    .table-responsive-emp {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .table-emp-custom {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .table-emp-custom th {
        color: #475569 !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.03em !important;
        padding: 14px 16px !important;
        border-bottom: 2px solid #e2e8f0 !important;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
    }

    .table-emp-custom td {
        padding: 12px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
        font-size: 0.875rem !important;
        color: #334155 !important;
        transition: background-color 0.15s ease !important;
    }

    .table-emp-custom tbody tr:hover {
        background-color: #f8fafc !important;
    }

    .table-emp-custom tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Action Buttons (Matching Lead/Opportunity design) */
    .btn-action-item {
        width: 36px !important;
        height: 36px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1.05rem !important;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        background: transparent;
        text-decoration: none !important;
        padding: 0 !important;
        outline: none !important;
        box-sizing: border-box !important;
        appearance: none !important;
        -webkit-appearance: none !important;
    }

    .btn-action-view {
        background-color: #EFF6FF !important;
        color: #2F53B4 !important;
        border-color: #DBEAFE !important;
    }

    .btn-action-view:hover {
        background-color: #2F53B4 !important;
        color: #ffffff !important;
        border-color: #2F53B4 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(47, 83, 180, 0.25) !important;
    }

    .btn-action-edit {
        background-color: #FFF7ED !important;
        color: #D97706 !important;
        border-color: #FED7AA !important;
    }

    .btn-action-edit:hover {
        background-color: #D97706 !important;
        color: #ffffff !important;
        border-color: #D97706 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25) !important;
    }

    .btn-action-delete {
        background-color: #FEF2F2 !important;
        color: #DC2626 !important;
        border-color: #FCA5A5 !important;
    }

    .btn-action-delete:hover {
        background-color: #DC2626 !important;
        color: #ffffff !important;
        border-color: #DC2626 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25) !important;
    }

    /* Header Inline Custom Dropdown Combo */
    .custom-dropdown-combo-header {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        height: 26px !important;
        width: 64px !important;
        cursor: pointer !important;
        vertical-align: middle !important;
    }

    .custom-dropdown-combo-header .dropdown-combo-input {
        height: 26px !important;
        width: 100% !important;
        padding: 0 14px 0 4px !important;
        font-size: 0.775rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        text-align: center !important;
        cursor: pointer !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        transition: all 0.15s ease !important;
        box-sizing: border-box !important;
    }

    .custom-dropdown-combo-header:hover .dropdown-combo-input,
    .custom-dropdown-combo-header.active .dropdown-combo-input {
        background-color: #ffffff !important;
        border-color: #2F53B4 !important;
        color: #2F53B4 !important;
        box-shadow: 0 2px 6px rgba(47, 83, 180, 0.12) !important;
    }

    .custom-dropdown-combo-header .custom-fp-combo-arrow {
        position: absolute !important;
        right: 5px !important;
        top: 50% !important;
        margin-top: -2px !important;
        border-color: #64748b transparent transparent transparent !important;
        border-style: solid !important;
        border-width: 4px 3.5px 0 3.5px !important;
        pointer-events: none !important;
        transition: transform 0.2s ease, border-color 0.15s ease !important;
    }

    .custom-dropdown-combo-header:hover .custom-fp-combo-arrow,
    .custom-dropdown-combo-header.active .custom-fp-combo-arrow {
        border-color: #2F53B4 transparent transparent transparent !important;
    }

    .custom-dropdown-combo-header.active .custom-fp-combo-arrow {
        transform: rotate(180deg) !important;
    }

    .custom-dropdown-combo-header .custom-dropdown-popup {
        min-width: 70px !important;
        top: calc(100% + 4px) !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12) !important;
    }

    .custom-dropdown-combo-header .custom-dropdown-item {
        padding: 6px 10px !important;
        font-size: 0.8rem !important;
        text-align: center !important;
    }

    /* Sub Add Row Buttons (Matching Lead/Opportunity design) */
    .btn-add-sub {
        background-color: #EFF6FF !important;
        color: #2F53B4 !important;
        border: 1px solid #DBEAFE !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        outline: none !important;
        box-sizing: border-box !important;
        height: 38px !important;
    }

    .btn-add-sub:hover {
        background-color: #2F53B4 !important;
        color: #ffffff !important;
        border-color: #2F53B4 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(47, 83, 180, 0.25) !important;
    }

    /* 38px Medium Custom Dropdown Combo (Matching btn-add-sub height) */
    .custom-dropdown-combo-sub {
        height: 38px !important;
    }

    .custom-dropdown-combo-sub .dropdown-combo-input {
        height: 38px !important;
        padding-right: 26px !important;
        border-radius: 8px !important;
    }

    .custom-dropdown-combo-sub .custom-fp-combo-arrow {
        right: 10px !important;
    }
    /* Full-size Actionbar Buttons (for show page actionbar) */
    .btn-emp {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 20px;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 10px;
        border: 1.5px solid transparent;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        line-height: 1.4;
        white-space: nowrap;
    }

    .btn-emp-primary {
        background: linear-gradient(135deg, #2F53B4 0%, #1E3A8A 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(47, 83, 180, 0.22);
    }
    .btn-emp-primary:hover {
        background: linear-gradient(135deg, #254394 0%, #172E6E 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(47, 83, 180, 0.35);
    }

    .btn-emp-info {
        background-color: #EFF6FF;
        color: #2F53B4;
        border-color: #DBEAFE;
    }
    .btn-emp-info:hover {
        background-color: #2F53B4;
        color: #ffffff;
        border-color: #2F53B4;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(47, 83, 180, 0.25);
    }

    .btn-emp-success {
        background-color: #ECFDF5;
        color: #059669;
        border-color: #A7F3D0;
    }
    .btn-emp-success:hover {
        background-color: #059669;
        color: #ffffff;
        border-color: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
    }

    .btn-emp-warning {
        background-color: #FFFBEB;
        color: #D97706;
        border-color: #FDE68A;
    }
    .btn-emp-warning:hover {
        background-color: #D97706;
        color: #ffffff;
        border-color: #D97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
    }

    .btn-emp-danger {
        background-color: #FEF2F2;
        color: #DC2626;
        border-color: #FCA5A5;
    }
    .btn-emp-danger:hover {
        background-color: #DC2626;
        color: #ffffff;
        border-color: #DC2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    }

    .btn-emp-light {
        background-color: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
    }
    .btn-emp-light:hover {
        background-color: #e2e8f0;
        color: #334155;
        border-color: #cbd5e1;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    /* Read-only field styling for show pages */
    .document-form .form-control[readonly],
    .document-form textarea[readonly],
    .document-form .form-control-sm[readonly],
    .form-control-sm.bg-light[readonly],
    .form-control.bg-light[readonly],
    textarea.form-control.bg-light[readonly] {
        background-color: #f8fafc !important;
        color: #64748b !important;
        border-color: #e9eef4 !important;
        cursor: default !important;
        box-shadow: none !important;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* Sales document redesign (SO / BN / DLV) — keep alongside emp styles */
    /* ═══════════════════════════════════════════════════════ */

    .biz-card-main {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        border: 1px solid #f1f5f9;
        padding: 32px;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }

    .section-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 8px;
        font-size: 1.15rem;
    }

    .contact-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 16px;
        background: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
    }

    .contact-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    .contact-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .contact-number {
        font-size: 0.9rem;
        font-weight: 700;
        color: #475569;
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
    }

    .btn-premium-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 9px 22px;
        border-radius: 10px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-premium-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        color: white;
    }

    .btn-premium-light {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        padding: 9px 22px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-premium-light:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .doc-table-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .document-lines-table thead tr,
    .invoice-table thead tr {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .document-lines-table thead th,
    .invoice-table thead th {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        padding: 12px 10px;
        vertical-align: middle;
        border-bottom: 2px solid #e2e8f0;
    }

    .document-lines-table tbody td,
    .document-lines-table tbody th,
    .invoice-table tbody td,
    .invoice-table tbody th {
        vertical-align: top;
        padding: 10px 8px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
    }

    .document-lines-table tbody tr:hover,
    .invoice-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .document-lines-table tbody .doc-line-no,
    .invoice-table tbody .doc-line-no {
        padding-top: 0.85rem;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
    }

    .doc-line-description-detail {
        font-size: 12px;
        line-height: 1.45;
        color: var(--vz-secondary-color, #64748b);
        margin-top: 0.35rem;
    }

    .doc-totals-grand .doc-totals-label {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .doc-totals-grand .form-control {
        font-size: 15px;
        font-weight: 700;
        color: #2563eb;
        border: 1.5px solid #93c5fd;
        background: #eff6ff !important;
    }

    .doc-actionbar .btn {
        font-weight: 600 !important;
        font-size: 13.5px !important;
        padding: 8px 16px !important;
        border-radius: 10px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s ease !important;
    }
    .doc-actionbar .btn-soft-warning,
    .doc-actionbar .btn-warning-subtle {
        background-color: #fff7ed !important;
        color: #c2410c !important;
        border: 1px solid #fed7aa !important;
    }
    .doc-actionbar .btn-soft-warning:hover {
        background-color: #ea580c !important;
        color: #ffffff !important;
        border-color: #ea580c !important;
    }
    .doc-actionbar .btn-soft-danger,
    .doc-actionbar .btn-danger-subtle {
        background-color: #fef2f2 !important;
        color: #b91c1c !important;
        border: 1px solid #fecaca !important;
    }
    .doc-actionbar .btn-soft-danger:hover {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        border-color: #dc2626 !important;
    }
    .doc-actionbar .btn-soft-info,
    .doc-actionbar .btn-info-subtle {
        background-color: #f0f9ff !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
    }
    .doc-actionbar .btn-soft-info:hover {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
    }
    .doc-actionbar .btn-light,
    .doc-actionbar .btn-outline-secondary {
        background-color: #f8fafc !important;
        color: #334155 !important;
        border: 1px solid #cbd5e1 !important;
    }
    .doc-actionbar .btn-light:hover,
    .doc-actionbar .btn-outline-secondary:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }
    .doc-actionbar .btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
    }
    .doc-actionbar .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4) !important;
        transform: translateY(-1px) !important;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }

        body, html {
            background: #ffffff !important;
            color: #0f172a !important;
            font-size: 11px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #page-topbar,
        .app-menu,
        .footer,
        .doc-actionbar,
        .page-title-box,
        .btn,
        #back-to-top,
        .breadcrumb,
        .alert,
        .no-print {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .page-content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .container-fluid, .col-12, .col-xl-10 {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            margin: 0 !important;
        }

        .card-body {
            padding: 10px 0 !important;
        }

        input.form-control,
        select.form-select,
        textarea.form-control,
        .form-control {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            font-size: 11px !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            box-shadow: none !important;
            min-height: auto !important;
            height: auto !important;
            resize: none !important;
        }

        .form-label {
            font-size: 11px !important;
            color: #64748b !important;
            font-weight: 500 !important;
            margin-bottom: 2px !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .document-lines-table thead th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-top: 1px solid #cbd5e1 !important;
            border-bottom: 2px solid #94a3b8 !important;
            font-weight: 700 !important;
            padding: 6px 8px !important;
            font-size: 11px !important;
        }

        .document-lines-table tbody td,
        .document-lines-table tbody th {
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 6px 8px !important;
            font-size: 11px !important;
        }

        #document-party-display > div > div {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 10px 12px !important;
            background: #ffffff !important;
        }

        .card-body, table, tr, #document-party-display {
            page-break-inside: avoid !important;
        }
    }
</style>
