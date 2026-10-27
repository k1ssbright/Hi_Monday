<style>
    .hero-banner-card {
        --hero-shadow: rgba(30, 58, 138, 0.22);
        --hero-btn: #1e3a8a;
        background: #1e3a8a;
        border-radius: 20px;
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px var(--hero-shadow);
        margin-bottom: 24px;
    }
    .hero-banner-card::before {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
        margin-bottom: 12px;
    }
    .btn-create-glow {
        background: #ffffff;
        color: var(--hero-btn);
        font-weight: 700;
        font-size: 14px;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .btn-create-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.28);
        background: #f8fafc;
        color: var(--hero-btn);
    }

    .stat-card-wow {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 18px;
        padding: 22px 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
    }
    .stat-card-wow:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.09);
        border-color: #cbd5e1;
    }
    .stat-card-wow::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        opacity: 0.9;
    }
    .stat-card-wow.primary::before { background: linear-gradient(90deg, #2563eb, #38bdf8); }
    .stat-card-wow.success::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card-wow.warning::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-card-wow.info::before { background: linear-gradient(90deg, #6366f1, #a855f7); }

    .stat-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .stat-card-wow:hover .stat-icon-wrap {
        transform: scale(1.1) rotate(4deg);
    }
    .stat-icon-wrap.primary { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #2563eb; }
    .stat-icon-wrap.success { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); color: #059669; }
    .stat-icon-wrap.warning { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); color: #d97706; }
    .stat-icon-wrap.info { background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); color: #4f46e5; }

    .filter-card-wow {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
    .toolbar-container {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }
    .search-input-wow {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        height: 44px;
        font-size: 13.5px;
        padding-left: 12px;
        transition: all 0.2s ease;
    }
    .search-input-wow:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }
    .select-wow {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        height: 44px;
        font-size: 13.5px;
        padding-left: 14px;
        font-weight: 500;
        color: #334155;
        transition: all 0.2s ease;
    }
    .select-wow:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .btn-filter-wow {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        height: 44px;
        padding: 0 20px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.22);
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-filter-wow:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.32);
        color: #ffffff;
    }

    .table-wow {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-wow thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-wow tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-wow tbody tr:hover {
        background-color: #f8fafc;
    }
    .table-wow td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .cust-info-box {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cust-avatar-box {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        border: 1px solid #dbeafe;
        flex-shrink: 0;
    }
    .cust-text-wrap {
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .cust-name-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cust-sub-desc {
        color: #64748b;
        font-size: 11.5px;
        line-height: 1.3;
        margin-top: 2px;
    }

    .doc-pill-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 13px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .doc-pill-tag:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
    }
    .doc-pill-tag:hover i {
        color: #ffffff !important;
    }

    .status-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-badge-pill.issued,
    .status-badge-pill.confirmed,
    .status-badge-pill.accepted,
    .status-badge-pill.approved,
    .status-badge-pill.sent,
    .status-badge-pill.closed,
    .status-badge-pill.qualified,
    .status-badge-pill.converted,
    .status-badge-pill.closed_won {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-badge-pill.draft,
    .status-badge-pill.pending_approval,
    .status-badge-pill.submitted,
    .status-badge-pill.superseded,
    .status-badge-pill.nurturing,
    .status-badge-pill.proposal,
    .status-badge-pill.negotiation {
        background-color: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .status-badge-pill.new,
    .status-badge-pill.contacting,
    .status-badge-pill.discovery,
    .status-badge-pill.needs_analysis {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }
    .status-badge-pill.voided,
    .status-badge-pill.cancelled,
    .status-badge-pill.rejected,
    .status-badge-pill.expired,
    .status-badge-pill.unqualified,
    .status-badge-pill.duplicate,
    .status-badge-pill.archived,
    .status-badge-pill.closed_lost {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .status-dot.issued,
    .status-dot.confirmed,
    .status-dot.accepted,
    .status-dot.approved,
    .status-dot.sent,
    .status-dot.closed,
    .status-dot.qualified,
    .status-dot.converted,
    .status-dot.closed_won { background-color: #10b981; }
    .status-dot.draft,
    .status-dot.pending_approval,
    .status-dot.submitted,
    .status-dot.superseded,
    .status-dot.nurturing,
    .status-dot.proposal,
    .status-dot.negotiation { background-color: #f59e0b; }
    .status-dot.new,
    .status-dot.contacting,
    .status-dot.discovery,
    .status-dot.needs_analysis { background-color: #2563eb; }
    .status-dot.voided,
    .status-dot.cancelled,
    .status-dot.rejected,
    .status-dot.expired,
    .status-dot.unqualified,
    .status-dot.duplicate,
    .status-dot.archived,
    .status-dot.closed_lost { background-color: #ef4444; }

    .action-btn-circle {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
        appearance: none;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-btn-circle:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
    }
</style>
