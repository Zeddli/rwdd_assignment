<?php
    session_start();
    if(isset($_COOKIE["loginInfo"])){
        $info = json_decode($_COOKIE["loginInfo"],true);
        $_SESSION["userInfo"] = $info;
        header("Location: ../HomePage/home.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include("../Head/Head.php");
    ?>
</head>
<body>
    <!-- Sticky Navbar -->
    <nav class="navbar" id="navbar">
        <div class="navbar-content">
            <a href="#" class="navbar-logo">
                <img src="assets/logo.png" alt="ProTask Logo">
                ProTask
            </a>
            
            <ul class="navbar-nav">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#demo">Demo</a></li>
                <li><a href="#team">Team</a></li>
            </ul>
            
            <div class="navbar-actions">
                <a href="../LoginSignup/landing/login/login.php" class="navbar-btn navbar-btn-outline">Log In</a>
                <a href="../LoginSignup/landing/signup/signup.php" class="navbar-btn navbar-btn-primary">Get Started</a>
                <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="hero">
        <div class="container">
            <div class="hero-content">
                <img src="assets/logo.png" alt="ProTask Logo" style="width: 120px; height: auto; margin-bottom: 30px; border-radius: 15px;">
                <h1>ProTask</h1>
                <p class="tagline">Collaborate. Create. Conquer.</p>
                <p class="value-prop">The ultimate project management platform that brings your team together with real-time collaboration, seamless task management, and powerful integrations.</p>
                <a href="../LoginSignup/landing/signup/signup.php" class="cta-button">Get Started Free</a>
                <div class="hero-image">
                    <div style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 15px; backdrop-filter: blur(10px);">
                        <i class="fas fa-tasks" style="font-size: 4rem; margin-bottom: 20px;"></i>
                        <h3>Dashboard Preview</h3>
                        <p>Experience the power of ProTask's intuitive interface</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Why Choose ProTask?</h2>
            <p class="section-subtitle">Powerful features designed to streamline your workflow and boost team productivity</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Real-time Collaboration</h3>
                    <p>Work together seamlessly with live chat, video calls, file sharing, and collaborative whiteboards. See changes as they happen.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Smart Task Management</h3>
                    <p>Organize projects with intuitive task boards, timelines, and automated workflows. Never miss a deadline again.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h3>Task Tracking</h3>
                    <p>Keep track of your tasks with due dates, assignees, and progress updates. Stay organized and never miss a deadline.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Smart Calendar</h3>
                    <p>Stay on top of your schedule with our intelligent calendar integration. Sync tasks with your favorite calendar apps and never miss an important deadline.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Advanced Analytics</h3>
                    <p>Track progress with detailed reports, time tracking, and performance insights to optimize your team's productivity.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile First</h3>
                    <p>Access your projects anywhere with our responsive design and native mobile apps for iOS and Android.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Get started in minutes with our simple 4-step process</p>
            
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Create Workspace</h3>
                    <p>Set up your team workspace in seconds. Choose from our professional templates or start from scratch.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Invite Team Members</h3>
                    <p>Add your team members with a simple invite link. They'll get instant access to collaborate.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Collaborate on Tasks</h3>
                    <p>Create tasks, assign responsibilities, and work together in real-time with our powerful collaboration tools.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Track Progress</h3>
                    <p>Monitor your team's progress with detailed analytics and automated reports. Celebrate your wins!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Demo Section -->
    <section id="product-demo" class="product-demo">
        <div class="container">
            <h2 class="section-title">See ProTask in Action</h2>
            <p class="section-subtitle">Experience the power of our platform with these key features</p>
            
            <div class="demo-container">
                <div class="demo-content">
                    <h3>Everything You Need in One Place</h3>
                    <p>ProTask combines the best of project management, team collaboration, and productivity tools into one seamless platform.</p>
                    
                    <ul class="demo-features">
                        <li>Interactive dashboard with real-time updates</li>
                        <li>Collaboration with team members</li>
                        <li>Comprehensive reporting and analytics</li>
                        <li>Mobile apps for iOS and Android</li>
                    </ul>
                </div>
                
                <div class="demo-image">
                    <div style="text-align: center;">
                        <!-- Demo of the ProTask platform -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="team">
        <div class="container">
            <h2 class="section-title">Meet Our Team</h2>
            <p class="section-subtitle">The passionate people behind ProTask</p>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="team-avatar">JS</div>
                    <h4>Sam Zhi Jian</h4>
                    <p class="role">Software Engineer</p>
                </div>
                
                <div class="team-member">
                    <div class="team-avatar">MJ</div>
                    <h4>Wong Kam Fatt</h4>
                    <p class="role">Software Engineer</p>
                </div>
                
                <div class="team-member">
                    <div class="team-avatar">DW</div>
                    <h4>Chong Yu Xuan</h4>
                    <p class="role">Software Engineer</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="cta-banner">
        <div class="container">
            <h2>Start Collaborating Today – It's Free!</h2>
            <p>Join thousands of teams who trust ProTask to manage their projects and boost productivity.</p>
            <a href="../LoginSignup/landing/signup/signup.php" class="cta-button">Get Started Free</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>ProTask</h3>
                    <p>The ultimate project management platform for modern teams. Collaborate, create, and conquer your goals together.</p>
                    
                </div>
                
                <div class="footer-section">
                    <h3>Product</h3>
                    <ul>
                        <li><a href="#hero">ProTask</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How it works</a></li>
                        <li><a href="#product-demo">Demo</a></li>
                        <li><a href="../LoginSignup/landing/signup/signup.php">Get Started</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Company</h3>
                    <ul>
                        <li><a href="#team">Our Team</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 ProTask. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for interactive elements -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Demo button functionality
        document.querySelector('.demo-image button').addEventListener('click', function() {
            alert('Demo feature coming soon! Sign up to get early access.');
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all feature cards and steps
        document.querySelectorAll('.feature-card, .step').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>
