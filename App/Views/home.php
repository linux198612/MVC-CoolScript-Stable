<div class="container mt-5">
    <!-- Hero Section -->
    <div class="text-center mb-5">
        <h1 class="display-3 fw-bold text-primary mb-3">
            Welcome to Coolscript MVC
        </h1>
        <p class="lead text-muted mb-4">
            A lightweight, secure, and developer-friendly PHP MVC framework
        </p>
        <span class="badge bg-primary fs-5 px-3 py-2">Version <?= htmlspecialchars($version) ?></span>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center mb-4">Core Features</h2>
        </div>
        <?php foreach ($features as $feature): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="display-4 mb-3"><?= $feature['icon'] ?></div>
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($feature['title']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars($feature['description']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Start Section -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Quick Start Guide</h3>
                    <ol class="list-group list-group-numbered list-group-flush">
                        <?php foreach ($quickstart as $step): ?>
                            <li class="list-group-item border-0 py-3">
                                <?= $step ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation Links -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h3 class="mb-4">Learn More</h3>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="/Documentation/readme.html" class="btn btn-primary btn-lg">
                    📚 Documentation
                </a>
                <a href="/Documentation/install.html" class="btn btn-outline-primary btn-lg">
                    ⚙️ Installation Guide
                </a>
                <a href="/Documentation/features.html" class="btn btn-outline-primary btn-lg">
                    ✨ Feature List
                </a>
                <a href="/Documentation/advanced.html" class="btn btn-outline-secondary btn-lg">
                    🚀 Advanced Features
                </a>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card bg-light border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">System Requirements</h5>
                    <div class="d-flex justify-content-around mt-3">
                        <div>
                            <strong>PHP</strong><br>
                            <span class="text-muted">8.2+</span>
                        </div>
                        <div>
                            <strong>MySQL</strong><br>
                            <span class="text-muted">Optional</span>
                        </div>
                        <div>
                            <strong>Apache</strong><br>
                            <span class="text-muted">mod_rewrite</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mt-5 mb-5">
        <div class="col-12 text-center">
            <div class="p-5 bg-primary text-white rounded-3">
                <h3 class="mb-3">Ready to Build Something Amazing?</h3>
                <p class="lead mb-4">Get started with Coolscript MVC Framework today!</p>
                <a href="/Documentation/install.html" class="btn btn-light btn-lg">
                    Get Started →
                </a>
            </div>
        </div>
    </div>
</div>
