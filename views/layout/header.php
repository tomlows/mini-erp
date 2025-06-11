<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini ERP - <?= $title ?? 'Sistema de Vendas' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Navbar Melhorada */
        .navbar {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
            color: #e3f2fd !important;
        }
        
        .navbar-brand i {
            margin-right: 0.6rem;
            font-size: 1.2rem;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.6rem 1rem !important;
            border-radius: 6px;
            transition: all 0.3s ease;
            margin: 0 0.2rem;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }
        
        .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .dropdown-menu {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            margin-top: 0.5rem;
            padding: 0.5rem 0;
        }
        
        .dropdown-item {
            color: #495057;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #007bff;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 0.5rem;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 0.7rem;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
            animation: pulse-badge 2s infinite;
        }
        
        @keyframes pulse-badge {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .navbar-toggler {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            padding: 0.3rem 0.6rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }
        
        /* Layout para Footer Fixo */
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        /* Footer Fixo */
        footer {
            margin-top: auto;
            background: linear-gradient(135deg, #343a40 0%, #495057 100%) !important;
            border-top: 3px solid #007bff;
        }
        
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #e9ecef;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #007bff;
        }
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        
        /* Rating Stars */
        .rating .fas.fa-star,
        .rating .fas.fa-star-half-alt {
            color: #ffc107;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .rating .far.fa-star {
            color: #dee2e6;
        }
        
        .product-card:hover .rating .fas.fa-star {
            animation: star-glow 0.3s ease;
        }
        
        @keyframes star-glow {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Popular Badge */
        .product-card .card-footer .text-success {
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }
        
        .product-card:hover .card-footer .text-success {
            opacity: 1;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <i class="fas fa-store"></i>
                Mini ERP
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (!isset($_GET['controller']) || $_GET['controller'] == 'Home') ? 'active' : '' ?>" href="<?= BASE_URL ?>">
                            <i class="fas fa-home me-2"></i>
                            Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['controller']) && $_GET['controller'] == 'Produto') ? 'active' : '' ?>" href="<?= BASE_URL ?>/?controller=Produto&action=index">
                            <i class="fas fa-boxes me-2"></i>
                            Produtos
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (isset($_GET['controller']) && $_GET['controller'] == 'Webhook') ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-exchange-alt me-2"></i>
                            Webhook
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?controller=Webhook&action=teste">
                                <i class="fas fa-flask"></i> Testar Webhook
                            </a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?controller=Webhook&action=documentacao">
                                <i class="fas fa-book"></i> Documentação
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/?controller=Webhook&action=healthCheck" target="_blank">
                                <i class="fas fa-heartbeat"></i> Health Check
                            </a></li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= BASE_URL ?>/?controller=Home&action=carrinho">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Carrinho
                            <?php if (isset($carrinho_quantidade) && $carrinho_quantidade > 0): ?>
                                <span class="cart-badge"><?= $carrinho_quantidade ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4"><?php 
    
    // Alert messages
    if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?> 