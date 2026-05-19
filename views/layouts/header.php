<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Food Ordering</title>
    <!-- Basic styling for student project -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        
        body { 
            font-family: 'Outfit', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #f8fafc; 
            color: #1e293b;
        }
        header { 
            background-color: #0055a5; /* Domino's Blue */
            color: #fff; 
            padding: 1.25rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border-bottom: 3px solid #e31837; /* Domino's Red */
        }
        header a { 
            color: #e2e8f0; 
            text-decoration: none; 
            margin-left: 1.5rem; 
            font-weight: 500;
            transition: all 0.2s ease;
        }
        header a:hover {
            color: #e31837;
        }
        .container { 
            max-width: 1200px; 
            margin: 3rem auto; 
            padding: 0 1.5rem; 
        }
        .alert { 
            padding: 1rem 1.5rem; 
            margin-bottom: 1.5rem; 
            border-radius: 8px; 
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .alert-error { background-color: #fff5f5; color: #e53e3e; border: 1px solid #fed7d7; }
        .alert-success { background-color: #f0fff4; color: #38a169; border: 1px solid #c6f6d5; }
        .card { 
            background: #fff; 
            padding: 2rem; 
            border-radius: 12px; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.02), 0 4px 6px -2px rgba(0, 0, 0, 0.01);
            margin-bottom: 2rem; 
            border: 1px solid #f1f5f9;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; 
            padding: 0.75rem 1rem; 
            border: 1.5px solid #cbd5e1; 
            border-radius: 8px; 
            box-sizing: border-box;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #e31837;
            box-shadow: 0 0 0 3px rgba(227, 24, 55, 0.15);
        }
        .btn { 
            display: inline-block; 
            padding: 0.75rem 1.5rem; 
            background: #0055a5; 
            color: #fff; 
            text-decoration: none; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600;
            font-family: inherit;
            text-align: center;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 85, 165, 0.2);
        }
        .btn:hover {
            background: #004080;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(0, 85, 165, 0.3);
        }
        .btn-danger { 
            background: #ef4444; 
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        }
        .btn-danger:hover {
            background: #dc2626;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
        }
        .btn-success { 
            background: #ea580c; /* Vibrant Food Orange */
            box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.2);
        }
        .btn-success:hover {
            background: #c2410c;
            box-shadow: 0 10px 15px -3px rgba(234, 88, 12, 0.3);
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        table, th, td { border: none; }
        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }
        th, td { padding: 1rem 1.25rem; text-align: left; }
        td { border-bottom: 1px solid #f1f5f9; color: #475569; }
        tr:hover td {
            background-color: #f8fafc;
        }
        .badge { 
            padding: 0.35rem 0.75rem; 
            border-radius: 9999px; 
            font-size: 0.75rem; 
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: none;
        }
        .badge-pending { background: #fef3c7; color: #d97706; }
        .badge-preparing { background: #e0f2fe; color: #0284c7; }
        .badge-delivery { background: #dbeafe; color: #2563eb; }
        .badge-delivered { background: #d1fae5; color: #059669; }
        .badge-active { background: #d1fae5; color: #059669; }
        .badge-inactive { background: #fee2e2; color: #dc2626; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; }
        .menu-card {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 0 !important;
        }
        .menu-card img { width: 100%; height: 220px; object-fit: cover; border-bottom: 1px solid #f1f5f9; transition: transform 0.3s ease; }
        .menu-card:hover img {
            transform: scale(1.03);
        }
        .menu-card > div {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="<?php echo url('/'); ?>" style="margin-left: 0; font-size: 1.5rem; font-weight: bold; color: #fff;">KhudaLagse?</a>
        </div>
        <?php // header uses url() helper to build links that work even when the app is in a subdirectory ?>
        <nav>
            <a href="<?php echo url('/menu'); ?>">Menu</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo url('/admin'); ?>">Admin Dashboard</a>
                <?php else: ?>
                    <a href="<?php echo url('/my-orders'); ?>">My Orders</a>
                <?php endif; ?>
                <a href="<?php echo url('/profile'); ?>">Profile</a>
                <a href="<?php echo url('/cart'); ?>">Cart (<span id="cart-count"><?php 
                    echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; 
                ?></span>)</a>
                <a href="<?php echo url('/logout'); ?>">Logout</a>
            <?php else: ?>
                <a href="<?php echo url('/login'); ?>">Login</a>
                <a href="<?php echo url('/register'); ?>">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
