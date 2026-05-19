<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Star Computer — <?php echo $__env->yieldContent('title'); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary:     #1a1f2e;
            --secondary:   #252b3b;
            --accent:      #4f8ef7;
            --accent-soft: #e8f0fe;
            --success:     #22c55e;
            --danger:      #ef4444;
            --warning:     #f59e0b;
            --text:        #1a1f2e;
            --text-muted:  #6b7280;
            --border:      #e5e7eb;
            --bg:          #f4f6fb;
            --white:       #ffffff;
            --sidebar-w:   260px;
            --font:        'Plus Jakarta Sans', sans-serif;
            --mono:        'JetBrains Mono', monospace;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font); background: var(--bg); color: var(--text); }
        .sidebar {
            width: var(--sidebar-w); height: 100vh;
            background: var(--primary); position: fixed;
            left: 0; top: 0; display: flex;
            flex-direction: column; z-index: 100; transition: all .3s;
        }
        .sidebar-brand { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-brand h4 { color: #fff; font-weight: 700; font-size: 1.1rem; letter-spacing: .5px; }
        .sidebar-brand span { color: var(--accent); }
        .sidebar-brand small { color: rgba(255,255,255,.4); font-size: .7rem; display: block; margin-top: 2px; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-label {
            color: rgba(255,255,255,.3); font-size: .65rem; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase; padding: 12px 8px 6px;
        }
        .nav-item a {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            color: rgba(255,255,255,.6); text-decoration: none;
            font-size: .875rem; font-weight: 500; transition: all .2s; margin-bottom: 2px;
        }
        .nav-item a:hover, .nav-item a.active { background: rgba(79,142,247,.15); color: #fff; }
        .nav-item a.active { color: var(--accent); }
        .nav-item a i { font-size: 1rem; width: 20px; text-align: center; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,.08); }
        .user-info {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,.06);
        }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--accent); display: flex; align-items: center;
            justify-content: center; color: #fff; font-weight: 700;
            font-size: .8rem; flex-shrink: 0;
        }
        .user-name { color: #fff; font-size: .8rem; font-weight: 600; }
        .user-role { color: rgba(255,255,255,.4); font-size: .7rem; }
        .main-content { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            background: var(--white); border-bottom: 1px solid var(--border);
            padding: 14px 28px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 1rem; font-weight: 700; color: var(--text); }
        .topbar-title small { color: var(--text-muted); font-weight: 400; font-size: .8rem; display: block; }
        .page-content { padding: 24px 28px; flex: 1; }
        .stat-card { background: var(--white); border-radius: 12px; padding: 20px; border: 1px solid var(--border); transition: all .2s; }
        .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.06); transform: translateY(-1px); }
        .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 12px; }
        .stat-value { font-size: 1.5rem; font-weight: 700; font-family: var(--mono); color: var(--text); }
        .stat-label { font-size: .78rem; color: var(--text-muted); font-weight: 500; margin-top: 2px; }
        .table-card { background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        .table-card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .table-card-title { font-size: .9rem; font-weight: 700; }
        .table thead th { background: var(--bg); font-size: .75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid var(--border); padding: 12px 16px; }
        .table tbody td { padding: 12px 16px; font-size: .855rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: var(--bg); }
        .badge-receivable { background: #fef2f2; color: var(--danger); padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-settled { background: #f0fdf4; color: var(--success); padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-payable { background: #fff7ed; color: var(--warning); padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-cash { background: #f0fdf4; color: var(--success); padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-credit { background: #fef2f2; color: var(--danger); padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-partial { background: #fff7ed; color: var(--warning); padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .btn-primary { background: var(--accent) !important; border-color: var(--accent) !important; font-weight: 600; font-size: .855rem; }
        .btn-sm { font-size: .775rem !important; }
        .form-card { background: var(--white); border-radius: 12px; border: 1px solid var(--border); padding: 24px; }
        .form-label { font-size: .8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
        .form-control, .form-select { border-color: var(--border); font-size: .855rem; border-radius: 8px; padding: 9px 12px; }
        .form-control:focus, .form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(79,142,247,.1); }
        .alert { border-radius: 10px; font-size: .855rem; border: none; }
        .memo-no { font-family: var(--mono); font-size: .8rem; font-weight: 600; color: var(--accent); background: var(--accent-soft); padding: 3px 8px; border-radius: 6px; }
        .low-stock { color: var(--danger) !important; font-weight: 700; }
        .logout-btn { background: none; border: none; color: rgba(255,255,255,.5); font-size: .8rem; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 6px 8px; border-radius: 6px; width: 100%; margin-top: 8px; transition: all .2s; }
        .logout-btn:hover { background: rgba(239,68,68,.15); color: #ef4444; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

        @media print {
    /* Sidebar hide karo */
    .sidebar { display: none !important; }

    /* Main content full width */
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
    }

    /* Topbar hide karo */
    .topbar { display: none !important; }

    /* Buttons hide karo */
    .btn, .no-print,
    .table-card-header .d-flex,
    .pay-form-wrap,
    .filter-bar,
    .actions-bar,
    form[action*="destroy"],
    .modal { display: none !important; }

    /* Full width table */
    .table-card,
    .form-card,
    .stat-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }

    /* Page break */
    @page { margin: 10mm; }

    body { background: white !important; }
}
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-pc-display-horizontal me-2"></i>Star <span>Computer</span></h4>
        <small>Accounting System</small>
    </div>

    <nav class="sidebar-nav">

<?php
    $isAdmin = auth()->user()->role === 'admin';
    $perms   = $isAdmin
        ? null
        : \App\Models\StaffPermission::where('user_id', auth()->id())->first();
?>
    
    <?php if($isAdmin || $perms?->dashboard_access): ?>
    <div class="nav-label">Main</div>
    <div class="nav-item">
        <a href="<?php echo e(route('dashboard')); ?>"
           class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
    </div>
    <?php endif; ?>

    
    <?php if($isAdmin || $perms?->sale_access || $perms?->purchase_access): ?>
    <div class="nav-label">Sales</div>
    <?php endif; ?>

    <?php if($isAdmin || $perms?->sale_access): ?>
    <div class="nav-item">
        <a href="<?php echo e(route('sales.pos')); ?>"
           class="<?php echo e(request()->routeIs('sales.*') ? 'active' : ''); ?>">
            <i class="bi bi-cart3"></i> Sale Point
        </a>
    </div>
    <?php endif; ?>

    <?php if($isAdmin || $perms?->purchase_access): ?>
    <div class="nav-item">
        <a href="<?php echo e(route('purchases.pos')); ?>"
           class="<?php echo e(request()->routeIs('purchases.*') ? 'active' : ''); ?>">
            <i class="bi bi-box-arrow-in-down"></i> Purchase Point
        </a>
    </div>
    <?php endif; ?>

    
    <?php if($isAdmin || $perms?->customer_access || $perms?->vendor_access || $perms?->inventory_access): ?>
    <div class="nav-label">Management</div>
    <?php endif; ?>

    <?php if($isAdmin || $perms?->customer_access): ?>
    <div class="nav-item">
        <a href="<?php echo e(route('customers.index')); ?>"
           class="<?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>">
            <i class="bi bi-people"></i> Customers
        </a>
    </div>
    <?php endif; ?>

    <?php if($isAdmin || $perms?->vendor_access): ?>
    <div class="nav-item">
        <a href="<?php echo e(route('vendors.index')); ?>"
           class="<?php echo e(request()->routeIs('vendors.*') ? 'active' : ''); ?>">
            <i class="bi bi-shop"></i> Vendors
        </a>
    </div>
    <?php endif; ?>

    <?php if($isAdmin || $perms?->inventory_access): ?>
    <div class="nav-item">
        <a href="<?php echo e(route('products.index')); ?>"
           class="<?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>">
            <i class="bi bi-box-seam"></i> Inventory
        </a>
    </div>
    <?php endif; ?>

    
    <?php if($isAdmin || $perms?->report_access): ?>
    <div class="nav-label">Reports</div>
    <div class="nav-item">
        <a href="<?php echo e(route('reports.daily')); ?>"
           class="<?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
            <i class="bi bi-bar-chart"></i> Daily Report
        </a>
    </div>
    <?php endif; ?>

    
    <?php if($isAdmin): ?>
    <div class="nav-label">Settings</div>
    <div class="nav-item">
        <a href="<?php echo e(route('settings.business')); ?>"
           class="<?php echo e(request()->routeIs('settings.business*') ? 'active' : ''); ?>">
            <i class="bi bi-gear"></i> Business Settings
        </a>
    </div>
    <div class="nav-item">
        <a href="<?php echo e(route('settings.permissions')); ?>"
           class="<?php echo e(request()->routeIs('settings.permissions*') ? 'active' : ''); ?>">
            <i class="bi bi-shield-lock"></i> Staff Permissions
        </a>
    </div>
    <div class="nav-item">
        <a href="<?php echo e(route('profile.edit')); ?>"
           class="<?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
            <i class="bi bi-person-circle"></i> My Profile
        </a>
    </div>
    <div class="nav-item">
        <a href="<?php echo e(route('staff.index')); ?>"
           class="<?php echo e(request()->routeIs('staff.*') ? 'active' : ''); ?>">
            <i class="bi bi-person-gear"></i> Staff Management
        </a>
    </div>
    <?php endif; ?>

</nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

            </div>
            <div>
                <div class="user-name"><?php echo e(auth()->user()->name); ?></div>
                <div class="user-role"><?php echo e(ucfirst(auth()->user()->role)); ?></div>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="topbar">
        <div>
            <div class="topbar-title"><?php echo $__env->yieldContent('title'); ?></div>
            <small class="text-muted" style="font-size:.75rem">
                <?php echo e(now()->format('l, d M Y')); ?>

            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if(session('success')): ?>
                <div class="alert alert-success py-2 px-3 mb-0">
                    <i class="bi bi-check-circle me-1"></i>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="page-content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\star_computer_accounting\resources\views/layouts/app.blade.php ENDPATH**/ ?>