<?php
// header.php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> | <?= $page_title ?? 'Home' ?></title>
    <meta name="description" content="<?= SITE_DESC ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<!-- Bootstrap JavaScript -->
 
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .navbar-nav {
            font-size: 20px;
        }
        .nav-item {
            padding-right: 10px;
        }
        .nav-link:hover {
            transform: scale(1.15);
        }
        .search-container {
            margin-right: 15px;
        }
        .search-input {
            border-radius: 20px;
            padding-left: 15px;
            width: 200px;
            transition: width 0.3s;
        }
        .search-input:focus {
            width: 250px;
        }
        .search-btn {
            margin-left: -40px;
            background: transparent;
            border: none;
            color: #6c757d;
        }

        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --purple: #6f42c1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        .text-purple {
            color: var(--purple) !important;
        }
        
        .bg-purple {
            background-color: var(--purple) !important;
        }
        
        .btn-outline-purple {
            color: var(--purple);
            border-color: var(--purple);
        }
        
        .btn-outline-purple:hover {
            color: white;
            background-color: var(--purple);
            border-color: var(--purple);
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        .card-header {
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
        
        .table th {
            border-top: none;
            background-color: rgba(0, 0, 0, 0.03);
            font-weight: 600;
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        .btn {
            font-weight: 500;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .alert {
            border: none;
            border-radius: 0.5rem;
        }
        
        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Animation for page load */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.775rem;
            }
        }


        /* Enhanced dropdown styling */
        .user-dropdown .dropdown-menu {
            min-width: 200px;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .user-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background-color 0.2s;
        }
        .user-dropdown .dropdown-item:hover {
            background-color: var(--light-color);
        }

.hover-dropdown:hover .dropdown-menu {
    display: block;
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.hover-dropdown .dropdown-menu {
    display: none;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
    pointer-events: auto;
}

/* Keep dropdown open when hovering over it */
.hover-dropdown .dropdown-menu:hover {
    display: block;
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container" style="max-width: 90%;">
            <a class="navbar-brand d-flex align-items-center" href="<?= url('index.php') ?>">
                <span class="fw-bold">SJCSI</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">   
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('index.php') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('about.php') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('gallery.php') ?>">Gallery</a>
                    </li>
                    
                    <!-- Offices Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="officesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Offices
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Link to the main offices page -->
                            <li><a class="dropdown-item" href="<?= url('OFFICEaccounting.php') ?>">Accounting Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEadmin.php') ?>">Administration Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEguidance.php') ?>">
                            Guidance Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEitsupport.php') ?>">
                            IT Support Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEnstp.php') ?>">
                            NSTP Office</a></li>
                             <li><a class="dropdown-item" href="<?= url('OFFICElibrary.php') ?>">
                            Library Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEregistrar.php') ?>">Registrar's Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEresearch.php') ?>">Research and Development Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEscholarship.php') ?>">
                            Scholarship Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEstudentaffairs.php') ?>">
                            Student Affairs Office</a></li>
                            <li><a class="dropdown-item" href="<?= url('OFFICEcampusministry.php') ?>">
                            Campus Ministry</a></li>
                        </ul>
                    </li>
                    
                    <!-- Departments Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="deptDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Departments
                        </a>
                        <ul class="dropdown-menu">
                            
                                    <li><a class="dropdown-item" href="<?= url('DEPARTMENTcaste.php') ?>">CASTE Department</a></li>
                                    <li><a class="dropdown-item" href="<?= url('DEPARTMENTcit.php') ?>">CIT Department</a></li>
                                    <li><a class="dropdown-item" href="<?= url('DEPARTMENTcoa.php') ?>">COA Department</a></li>
                             
                                    <li><a class="dropdown-item" href="<?= url('DEPARTMENTcba.php') ?>">CBA Department</a></li>
                                    <li><a class="dropdown-item" href="<?= url('DEPARTMENTcje.php') ?>">CJE Department</a></li>
                                    <li><a class="dropdown-item" href="<?= url('DEPARTMENTshs.php') ?>">SHS Department</a></li>
                                    <li><a class="dropdown-item" href="<?= url('DEPARTMENTjhs.php') ?>">JHS Department</a></li>
                      
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('academic.php') ?>">Academic</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://sjc.wela.online/" target="_blank">WELA</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <div class="search-container">
                        <form class="d-flex" role="search" action="<?= url('search.php') ?>">
                            <input class="form-control search-input" type="search" placeholder="Search..." aria-label="Search" name="q">
                            <button class="search-btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
    <div class="dropdown ms-2 user-dropdown hover-dropdown">
        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" 
                aria-expanded="false">
            <i class="fas fa-user me-1"></i>
            <?= htmlspecialchars($_SESSION['user']['email'] ?? 'Account') ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <?php if (isset($_SESSION['user']['role'])): ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li><a class="dropdown-item" href="<?= url('AdminDashboard.php') ?>">
                        <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                <?php elseif ($_SESSION['user']['role'] === 'department' && !empty($_SESSION['user']['department'])): ?>
                    <li><a class="dropdown-item" href="<?= url('DEPARTMENT' . strtolower(str_replace(' ', '-', $_SESSION['user']['department'])) . '_dashboard.php') ?>">
                        <i class="fas fa-tachometer-alt me-2"></i>Department Dashboard</a></li>
                <?php elseif ($_SESSION['user']['role'] === 'office' && !empty($_SESSION['user']['office'])): ?>
                    <li><a class="dropdown-item" href="<?= url('OFFICE' . strtolower(str_replace(' ', '-', $_SESSION['user']['office'])) . '_dashboard.php') ?>">
                        <i class="fas fa-tachometer-alt me-2"></i>Office Dashboard</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="<?= url('dashboard.php') ?>">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                <?php endif; ?>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= url('logout.php') ?>">
                <i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
    </div>
<?php else: ?>
    <a href="<?= url('login.php') ?>" class="btn btn-outline-light ms-2">
        <i class="fas fa-sign-in-alt me-2"></i>Login
    </a>
<?php endif; ?>

                </div>
            </div>
        </div>
    </nav>
    
</script>
    <main>