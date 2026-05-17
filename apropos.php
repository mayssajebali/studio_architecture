<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Home</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"></head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<title>Architecture d'Intérieur - Notre Histoire</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<title>Nos valeurs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Notre Équipe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Méthodologie de travail</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Pourquoi nous choisir ?</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">



<body style="padding-top: 80px;">
  <header>
        <?php include 'navbar.php'; ?>
    </header> 

<!-- Bannière À PROPOS -->
<section class="apropos-hero">
    <div class="apropos-overlay"></div>
    <h1 class="fade-in-title">A PROPOS DE NOTRE BUREAU D'ETUDES</h1>
</section>


<!-- Section Histoire -->
<section class="histoire-section">
    <div class="container-fluid">
        <div class="row align-items-center">
            
            
            <!-- Colonne gauche - Texte -->
            <div class="col-lg-6 col-xl-5 offset-xl-1">
                <div class="content-text-wrapper">


                    <!-- Label ABOUT US avec trait -->
                    <div class="about-us-label">
                        <div class="label-line"></div>
                        <span class="label-text">QUI SOMMES NOUS ?</span>
                    </div>
                    
                    <!-- Titre avec ligne décorative -->
                    <div class="title-container">
                        <h2 class="main-title">
                            L'architecture d'intérieur... <span class="italic-highlight">Une histoire de passion</span>
                        </h2>
                        <div class="title-line"></div>
                    </div>
                    
                    <!-- Premier paragraphe -->
                    <p class="description-paragraph first-para">
                        Crée en <strong>2013</strong>, notre bureau d’études d’architecture intérieure est né d’une passion commune pour le design, la fonctionnalité et l’innovation.
                    
                    <p class="description-paragraph">
                        <strong>À ses débuts</strong>, l’équipe travaillait principalement sur de petits projets résidentiels, avec la volonté de transformer chaque espace en un lieu <strong>unique</strong>, harmonieux et agréable à vivre.
                    </p>
                    
                    <!-- Deuxième paragraphe -->
                    <p class="description-paragraph second-para">
                        <strong>Aujourd'hui</strong>, fort de plus de <strong>10 ans</strong> d'expérience, nous avons réalisé de nombreux projets dans des styles variés, tout en conservant une approche centrée sur l’écoute du client et la qualité.

                    </p>
                    
                    <!-- Bouton -->
                    <button class="btn-en-savoir-plus">
                        <span class="btn-icon-left"></span>
                        EN SAVOIR +
                        <span class="btn-icon-right"></span>
                    </button>
                    
                </div>
            </div>
            
            <!-- Colonne droite - Images Style Maquette 1 -->
            <div class="col-lg-6 col-xl-6">
                <div class="images-container-style1">
                    
                    <!-- Badge circulaire décoratif -->
                    <div class="circular-badge">
                        <svg class="circular-text" viewBox="0 0 200 200">
                            <path id="circlePath" d="M 100, 100 m -80, 0 a 80,80 0 1,1 160,0 a 80,80 0 1,1 -160,0" fill="none"/>
                            <text>
                                <textPath href="#circlePath" startOffset="0%">
                                    STUDIO DE DESIGN D'INTÉRIEUR • PRIMÉ •
                                </textPath>
                            </text>
                        </svg>
                        <div class="badge-icon">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    
                    <!-- Image 1 - Cuisine -->
                    <div class="image-box image-kitchen">
                        <img src="Images/Rectangle 36.png" alt="Cuisine moderne">
                    </div>
                    
                    <!-- Image 2 - Chambre -->
                    <div class="image-box image-bedroom">
                        <img src="Images/eclectic-boho-room.jpg" alt="Chambre moderne">
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Section Statistiques (de la maquette 1) -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            
            <!-- Stat 1 -->
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="stat-item">
                    <div class="stat-number">
                        <span class="counter">15</span><span class="plus-sign">+</span>
                    </div>
                    <div class="stat-label">
                        <span class="label-bold">EXPERTS</span><br>
                        <span class="label-light">PROFESSIONNELS</span>
                    </div>
                </div>
            </div>
            
            <!-- Stat 2 -->
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="stat-item">
                    <div class="stat-number">
                        <span class="counter">50</span><span class="plus-sign">+</span>
                    </div>
                    <div class="stat-label">
                        <span class="label-bold">PROJETS</span><br>
                        <span class="label-light">REALISES</span>
                    </div>
                </div>
            </div>
            
            <!-- Stat 3 -->
            <div class="col-md-4">
                <div class="stat-item">
                    <div class="stat-number">
                        <span class="counter">10</span><span class="plus-sign">+</span>
                    </div>
                    <div class="stat-label">
                        <span class="label-bold">ANS</span><br>
                        <span class="label-light">D'EXPERIENCE</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>






<section class="philosophy-section">
        <div class="container">
            <h2 class="section-title2">Nos valeurs</h2>
            <div class="title-underline"></div>
            
            <div class="row g-4 justify-content-center align-items-center">
                <!-- Créativité -->
                <div class="col">
                    <div class="philosophy-item">
                        <div class="icon-wrapper">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 3h7v7H3z"/>
                                <path d="M14 3h7v7h-7z"/>
                                <path d="M14 14h7v7h-7z"/>
                                <path d="M3 14h7v7H3z"/>
                            </svg>
                        </div>
                        <h3 class="philosophy-title">Créativité</h3>
                    </div>
                </div>

                <!-- Écoute du client -->
                <div class="col">
                    <div class="philosophy-item">
                        <div class="icon-wrapper">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3 class="philosophy-title">Écoute du client</h3>
                    </div>
                </div>

                <!-- Qualité -->
                <div class="col">
                    <div class="philosophy-item">
                        <div class="icon-wrapper">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h3 class="philosophy-title">Qualité</h3>
                    </div>
                </div>

                <!-- Innovation -->
                <div class="col">
                    <div class="philosophy-item">
                        <div class="icon-wrapper">
                            <svg viewBox="0 0 24 24">
                                <path d="M9 18h6"/>
                                <path d="M10 22h4"/>
                                <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8c0-3.31-2.69-6-6-6S6 4.69 6 8c0 1.44.52 2.77 1.39 3.79.76.76 1.23 1.52 1.41 2.5"/>
                            </svg>
                        </div>
                        <h3 class="philosophy-title">Innovation</h3>
                    </div>
                </div>

                <!-- Respect des délais -->
                <div class="col">
                    <div class="philosophy-item">
                        <div class="icon-wrapper">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <h3 class="philosophy-title">Respect des délais</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="team-section">
        <div class="container team-container">
            <h2 class="main-title"><span class="italic-highlight">L'ÉQUIPE</span></h2>

            <div class="row g-4">
                <!-- Membre 1 - Femme -->
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=800" 
                             alt="Zahredine MEZIANE" 
                             class="member-image">
                        <div class="member-info">
                            <h3 class="member-name">Sarah BENALI</h3>
                            <p class="member-position">Directrice, Architecte Designer</p>
                            <p class="member-description">
                                Spécialiste des ambiances modernes et du sur-mesure, Sarah supervise la conception des espaces et le choix des matériaux.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Membre 2 - Femme -->
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=800" 
                             alt="Sarah NEFZI" 
                             class="member-image">
                        <div class="member-info">
                            <h3 class="member-name">Lina SLIMANE</h3>
                            <p class="member-position">Chef de projet</p>
                            <p class="member-description">
                                Elle coordonne les étapes de réalisation, assure le lien avec les fournisseurs et veille au respect des délais et du budget.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Membre 3 - Homme -->
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=800" 
                             alt="Soumia SALOU" 
                             class="member-image">
                        <div class="member-info">
                            <h3 class="member-name">Yassine BELKACEM</h3>
                            <p class="member-position">Designer 3D</p>
                            <p class="member-description">
                                Expert en modélisation, il réalise des rendus 3D réalistes, des plans techniques et des visites virtuelles.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>






    <section class="process-section">
        <div class="container">
            <div class="section-content">
                <!-- Partie gauche -->
                <div class="left-content">
                    <h2 class="section-title1">Méthodologie de travail</h2>
                    <div class="title-underline"></div>
                </div>

                <!-- Partie droite - Timeline -->
                <div class="right-content">
                    <div class="timeline">
                        <!-- Item 1 -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Analyse des besoins</h3>
                                <p>Écoute active, visite du site, relevés et compréhension les besoins du client.</p>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Proposition de moodboards</h3>
                                <p>Planche d’inspiration incluant couleurs, matières, ambiances et premières idées d’aménagement.</p>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Conception 3D réaliste</h3>
                                <p>Création de rendus détaillés permettant de visualiser l’espace avant travaux.</p>
                            </div>
                        </div>

                        <!-- Item 4 -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Optimisation des plans techniques</h3>
                                <p>Plans électriques, mobiliers, matériaux et solutions d’optimisation de l’espace.</p>
                            </div>
                        </div>

                        <!-- Item 5 -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Suivi de chantier</h3>
                                <p>Notre équipe assure le suivi complet de votre projet et coordonne les différents corps de métier</p>
                            </div>
                        </div>

                        <!-- Item 6 -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Livraison finale</h3>
                                <p>Présentation du projet finalisé et validation avec le client.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Pourquoi nous choisir ?</h2>
            <div class="title-underline"></div>
            
            <div class="row g-4">
                
                <div class="col-lg-3 col-md-6">
                    <div class="category-card">
                        <div class="why-icon">
                            <i class="fas fa-handshake"></i>
                        </div>

                        <h4 class="category-title">Suivi personnalisé tout au long du projet</h4>
        
                    </div>
                </div>

                
                <div class="col-lg-3 col-md-6">
                    <div class="category-card">
                        <div class="why-icon">
                            <i class="fas fa-cube"></i>
                        </div>
                        <h4 class="category-title">Rendus 3D réalistes et visites virtuelles pour une projection fidèle</h4>
                    
                    </div>
                </div>

                
                <div class="col-lg-3 col-md-6">
                    <div class="category-card">
                        <div class="why-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4 class="category-title">Intégration des tendances dans nos conceptions</h4>
                        
                    </div>
                </div>

                
                <div class="col-lg-3 col-md-6">
                    <div class="category-card">
                        <div class="why-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h4 class="category-title">Large palette de styles : moderne, minimaliste, classique...</h4>
                        
                    </div>
                </div>
            </div>
    



        <!-- Bouton Voir nos services -->
            <div class="btn-container">
                <a href="services.html" class="btn-voir-services">
                    VOIR NOS SERVICES →
                    <span class="btn-icon-right"></span>
                    
                </a>
            </div>
        </div>
    </section>



<footer>
    <div class="container">
        <div id="div1footer" class="row justify-content-between">

            <div class="col-md-3">
                <img src="images/logo.png" alt="Logo" height="40">
                <p class="mt-3" class="pfooter">
                    Nous proposons une gamme complète de services de design d'intérieur 
                    et de conception architecturale.
                </p>
                <a href="https://www.facebook.com" target="_blank"><i class="bi bi-facebook me-4"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="bi bi-instagram me-4"></i></a>
                <a href="https://www.linkedin.com" target="_blank"><i class="bi bi-linkedin me-4"></i></a>
                <a href="https://www.youtube.com" target="_blank"><i class="bi bi-youtube me-4"></i></a>
            </div>

            <div class="col-md-2">
                <p class="p_gris" style="font-size: 16px;">Liens importants</p>
                <p class="pfooter"><a href="accueil.html" style="font-style: none;">Accueil</a></p>
                <p class="pfooter"><a href="apropos.html">À propos</a></p>
                <p class="pfooter"><a href="services.html">Services</a></p>
                <p class="pfooter"><a href="portfolio/portfolio.html">Portfolio</a></p>
                <p class="pfooter"><a href="contact/contact.html">Contact</a></p>
            </div>

            <div class="col-md-4">
                <p class="p_gris" style="font-size: 16px;">Contact</p>
                <p class="pfooter"><i class="bi bi-geo-alt"></i> 2972 Westheimer Rd. Santa Ana, Illinois 85486</p>
                <p class="pfooter"><i class="bi bi-telephone"></i> +216 77 715 720</p>
                <p class="pfooter"><a href="mailto:info@greenhome.com" id="mail"><i class="bi bi-envelope"></i> info@greenhome.com</a></p>
            </div>

        </div>

    </div>
    <div id="div2footer">
        Copyright © 2025 GreenHome LLP. Tous droits réservés.
    </div>
</footer>

</body>
</html>
