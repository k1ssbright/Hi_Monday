<style>
    /* ═══════════════════════════════════════════════════════ */
    /* Shared Modern Design System for Document Index Pages   */
    /* (Consistent with Partner-Index & Partner-Create)        */
    /* ═══════════════════════════════════════════════════════ */

    .biz-card-main {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        padding: 24px;
    }

    .btn-add-biz {
        background-color: #3b5998;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 10px 22px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(59, 89, 152, 0.2);
        text-decoration: none;
    }

    .btn-add-biz:hover {
        background-color: #2d4373;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(59, 89, 152, 0.3);
        transform: translateY(-1px);
    }

    .filter-label-biz {
        font-size: 0.825rem;
        font-weight: 500;
        color: #64748b;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .filter-input-biz, .filter-select-biz {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 14px;
        font-size: 0.875rem;
        color: #1e293b;
        height: 42px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .filter-input-biz:focus, .filter-select-biz:focus {
        border-color: #3b5998;
        box-shadow: 0 0 0 3px rgba(59, 89, 152, 0.12);
        outline: 0;
    }

    .filter-input-biz::placeholder {
        color: #94a3b8;
    }

    .btn-search-navy-biz {
        background-color: #3b5998;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 9px 24px;
        font-size: 0.875rem;
        font-weight: 600;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background-color 0.2s;
    }

    .btn-search-navy-biz:hover {
        background-color: #2d4373;
        color: #ffffff;
    }

    .btn-reset-filter-biz {
        background-color: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 0.875rem;
        font-weight: 500;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: background-color 0.2s, color 0.2s;
    }

    .btn-reset-filter-biz:hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }

    /* Modern Table Styling */
    .table-biz-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-biz-custom th {
        color: #475569;
        font-size: 0.825rem;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        background-color: #f8fafc;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-biz-custom td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table-biz-custom tbody tr:nth-child(odd) td {
        background-color: #ffffff;
    }

    .table-biz-custom tbody tr:nth-child(even) td {
        background-color: #f8fafc;
    }

    .table-biz-custom tbody tr:hover td {
        background-color: #f1f5f9;
    }

    /* Select2 Customization for Filters */
    .select2-container--default .select2-selection--single.select2-biz-filter {
        height: 42px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        padding: 6px 14px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single.select2-biz-filter,
    .select2-container--default.select2-container--open .select2-selection--single.select2-biz-filter {
        border-color: #3b5998 !important;
        box-shadow: 0 0 0 3px rgba(59, 89, 152, 0.12) !important;
        outline: 0 !important;
    }

    .select2-container--default .select2-selection--single.select2-biz-filter .select2-selection__rendered {
        color: #1e293b !important;
        font-size: 0.875rem !important;
        line-height: 28px !important;
        padding-left: 0 !important;
    }

    .select2-container--default .select2-selection--single.select2-biz-filter .select2-selection__placeholder {
        color: #94a3b8 !important;
        font-size: 0.875rem !important;
    }

    .select2-container--default .select2-selection--single.select2-biz-filter .select2-selection__arrow {
        height: 40px !important;
        right: 10px !important;
    }

    .select2-container--default .select2-selection--single.select2-biz-filter .select2-selection__clear {
        margin-right: 20px;
        color: #94a3b8;
        font-size: 16px;
    }

    /* Status Badges */
    .badge-status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-status-draft {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .badge-status-confirmed, .badge-status-issued, .badge-status-accepted {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-status-sent {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .badge-status-cancelled, .badge-status-voided, .badge-status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .badge-status-closed, .badge-status-superseded {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* Action icon buttons */
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        border: none;
        color: #ffffff;
        transition: opacity 0.2s, transform 0.15s;
        text-decoration: none;
    }

    .btn-action-icon:hover {
        opacity: 0.9;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-action-view { background-color: #3b5998; }
    .btn-action-edit { background-color: #ef5350; }
    .btn-action-print { background-color: #64748b; }

        /* Card Main */
        .emp-card-main {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            padding: 24px;
        }

        /* Buttons */
        .btn-add-emp {
            background: linear-gradient(135deg, #2F53B4 0%, #1E3A8A 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(47, 83, 180, 0.28);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .btn-add-emp:hover {
            background: linear-gradient(135deg, #254394 0%, #172E6E 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(47, 83, 180, 0.38);
        }

        /* Secondary Button */
        .btn-sec-emp {
            background-color: #ffffff;
            color: #2F53B4;
            border: 1.5px solid #2F53B4;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(47, 83, 180, 0.08);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .btn-sec-emp:hover {
            background-color: #EFF6FF;
            color: #1E3A8A;
            border-color: #1E3A8A;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(47, 83, 180, 0.18);
        }

        /* Filters Box */
        .filter-box-emp {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 24px;
        }

        .filter-label-emp {
            font-size: 0.825rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-input-emp {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px;
            height: 42px !important;
            padding: 0 14px;
            font-size: 0.875rem;
            color: #0f172a !important;
            transition: all 0.2s ease;
            box-sizing: border-box;
            box-shadow: none;
        }

        .filter-input-emp,
        .filter-input-emp:valid,
        .filter-input-emp:invalid,
        .filter-input-emp:active,
        .filter-input-emp:-webkit-autofill,
        .filter-input-emp:-webkit-autofill:hover,
        .filter-input-emp:-webkit-autofill:focus {
            color: #0f172a !important;
            background-color: #ffffff !important;
        }

        .filter-input-emp:focus {
            border-color: #2F53B4 !important;
            box-shadow: 0 0 0 3.5px rgba(47, 83, 180, 0.14) !important;
            outline: none !important;
        }

        .btn-search-navy-emp {
            background: linear-gradient(135deg, #2F53B4 0%, #1E3A8A 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            height: 42px !important;
            padding: 0 22px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(47, 83, 180, 0.22);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .btn-search-navy-emp:hover {
            background: linear-gradient(135deg, #254394 0%, #172E6E 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(47, 83, 180, 0.32);
        }

        /* Table Responsive Container */
        .table-responsive-emp {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        /* Table Styles */
        .table-emp-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table-emp-custom th {
            color: #475569;
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 16px 18px;
            border-bottom: 2px solid #e2e8f0;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-emp-custom td {
            padding: 16px 18px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.875rem;
            color: #334155;
            transition: background-color 0.15s ease;
        }

        .table-emp-custom tbody tr {
            transition: all 0.15s ease;
        }

        .table-emp-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-emp-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .table-emp-custom .form-check-input {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .table-emp-custom .form-check-input:checked {
            background-color: #2F53B4;
            border-color: #2F53B4;
        }

        .emp-name-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.9rem;
            line-height: 1.3;
        }

        .emp-email-sub {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2px;
        }

        .emp-code-badge {
            font-family: 'SFMono-Regular', Consolas, Monaco, monospace;
            font-size: 0.8rem;
            font-weight: 600;
            color: #2F53B4;
            background: #EFF6FF;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #DBEAFE;
            display: inline-block;
            letter-spacing: 0.02em;
        }

        /* Status Badges */
        .badge-status-active {
            background-color: #ECFDF5 !important;
            color: #059669 !important;
            border: 1px solid #A7F3D0 !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-inactive {
            background-color: #FEF2F2 !important;
            color: #DC2626 !important;
            border: 1px solid #FCA5A5 !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-warning {
            background-color: #FFFBEB !important;
            color: #D97706 !important;
            border: 1px solid #FDE68A !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-blue {
            background-color: #EFF6FF !important;
            color: #2563EB !important;
            border: 1px solid #BFDBFE !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Action Buttons */
        .btn-action-item {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            background: transparent;
            text-decoration: none;
        }

        .btn-action-view {
            background-color: #EFF6FF;
            color: #2F53B4;
            border-color: #DBEAFE;
        }

        .btn-action-view:hover {
            background-color: #2F53B4;
            color: #ffffff;
            border-color: #2F53B4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(47, 83, 180, 0.25);
        }

        .btn-action-edit {
            background-color: #FFF7ED;
            color: #D97706;
            border-color: #FED7AA;
        }

        .btn-action-edit:hover {
            background-color: #D97706;
            color: #ffffff;
            border-color: #D97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        }

        .btn-action-delete {
            background-color: #FEF2F2;
            color: #DC2626;
            border-color: #FCA5A5;
        }

        .btn-action-delete:hover {
            background-color: #DC2626;
            color: #ffffff;
            border-color: #DC2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        /* Pagination Styling */
        .pagination-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 18px;
            margin-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .pagination-info-text {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Custom Dropdown Combo Component (Register Page Style) */
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
            max-height: 155px !important;
            overflow-y: scroll !important;
            display: none;
            padding: 6px 0 !important;
            text-align: left !important;
            scrollbar-width: thin !important;
            scrollbar-color: #2F53B4 #E2E8F0 !important;
        }

        /* Custom Visible Scrollbar Track & Thumb for Dropdown Popup */
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

        .custom-dropdown-popup::-webkit-scrollbar-thumb:hover {
            background: #1E3A8A !important;
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
    </style>
