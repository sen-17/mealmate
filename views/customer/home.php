<main>
    <section id="hero-menu-section" class="d-flex align-items-center">
        <div class="container" id="home-container">
            <div class="hero-content">
                <p class="home-hero-slogan">KATERING HARIAN HIDUP LEBIH MUDAH</p>

                <h1 id="home-slogan-1">
                    Makan Enak, <br class="d-none d-md-inline">
                    <span id="home-slogan-2">Aktivitas Makin Lancar</span>
                </h1>

                <p id="home-slogan-description">
                    Mealmate hadir untuk menemani hari-harimu dengan menu katering rumahan yang sehat, lezat, dan selalu fresh. Cocok untuk mahasiswa dan pekerja Indonesia.
                </p>

                <button class="home-cto-btn btn d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-fork-knife"></i>
                    <span>Lihat Menu Hari Ini</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </section>

    <section id="home-information" class="py-4 py-md-5">
        <div class="container">
            <div class="row gy-4 gx-sm-4 feature-wrapper">
                <?php foreach ($features as $feature): ?>
                    <div class="col-12 col-sm-6 col-lg-3 info-card-col">
                        <div class="info-card">
                            <div class="icon-logo">
                                <img src="<?= htmlspecialchars($feature['icon']) ?>" alt="<?= htmlspecialchars($feature['title']) ?>" loading="lazy">
                            </div>

                            <div class="info-description mt-3">
                                <h4><?= htmlspecialchars($feature['title']) ?></h4>
                                <p><?= htmlspecialchars($feature['desc']) ?></p>
                            </div>  
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="home-order-steps" class="py-4 py-md-5">
        <div class="container">
            <div class="steps-wrapper">
                <div class="row gy-4 align-items-stretch">
                    <div class="steps-title-col col-12 col-xl">
                        <div class="steps-title">
                            <h4>Cara Mudah Pesan di MealMate</h4>
                            <p>Hanya dalam beberapa langkah, makan enak sudah siap di depan mata</p>
                        </div>
                    </div>
                    <?php foreach ($steps as $step): ?>
                        <div class="col-12 col-sm-6 col-lg-3 col-xl info-card-col">
                            <div class="info-card">
                                <div class="steps-no-logo d-flex align-items-center">
                                    <div class="steps-seq"><?= htmlspecialchars($step['no']) ?></div>
                                    <div class="icon-logo">
                                        <img src="<?= htmlspecialchars($step['icon']) ?>" alt="<?= htmlspecialchars($step['title']) ?>" loading="lazy">
                                    </div>
                                </div>

                                <div class="info-description mt-3">
                                    <h4><?= htmlspecialchars($step['title']) ?></h4>
                                    <p><?= htmlspecialchars($step['desc']) ?></p>
                                </div>  
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section id="home-banner-cta" class="py-4 py-md-5">
        <div class="container">
            <div class="home-banner-container px-4 px-md-5 py-4 py-md-5">
                <div class="banner-content d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                    <div class="banner-slogan">
                        <h3>Makan Sehat, <br> Hidup Lebih Semangat</h3>
                    </div>
                    <div class="banner-cto">
                        <p>Bergabunglah dengan ribuan mahasiswa dan pekerja lainnya yang sudah merasakan kemudahan dan kelezatan MealMate setiap hari.</p>
                        <button class="btn banner-cto-btn">
                            Mulai Pesan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="home-favorite-package" class="py-4 py-md-5">
        <div class="container">
            <div class="home-package-title-wrapper d-flex flex-column align-items-center text-center">
                <p>PAKET POPULER</p>
                <div class="package-title d-flex align-items-center justify-content-center">
                    <img src="./assets/svg/leaf.svg" alt="leaf" loading="lazy">
                    <h4>Pilihan Favorit Kami</h4>
                    <img src="./assets/svg/leaf.svg" alt="leaf" loading="lazy">
                </div>
                <p>Paket terbaik yang paling banyak dipesan oleh pelanggan kami</p>
            </div>

            <div class="row mt-3 mt-md-4 g-3 g-md-4 package-list-row mx-auto">
                <?php foreach ($packages as $package): ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="package-card">
                            <div class="img-placeholder">
                                <img src="<?= htmlspecialchars($package['img']) ?>" alt="<?= htmlspecialchars($package['title']) ?>">
                            </div>
                            <div class="package-info">
                                <p><?= htmlspecialchars($package['title']) ?></p>
                                <div class="package-duration-slot d-flex gap-2">
                                    <p><?= htmlspecialchars($package['duration_days']) ?></p>
                                    <p>|</p>
                                    <p><?= htmlspecialchars($package['applicable_days']) ?></p>
                                </div>
                                <p><?= htmlspecialchars($package['description']) ?></p>

                                <div class="package-price">
                                    <p>Mulai dari</p>
                                    <p>Rp <?= htmlspecialchars($package['price']) ?> <span>/ Minggu</span></p>
                                </div>

                                <button class="btn">
                                    Pilih Paket
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>                
            </div>
        </div>
    </section>

    <section id="home-reviews" class="py-4 py-md-5">
        <div class="container">
            <div class="home-reviews-wrapper d-flex flex-column align-items-center text-center">
                <div class="package-title d-flex align-items-center justify-content-center">
                    <img src="./assets/svg/leaf.svg" alt="leaf" loading="lazy">
                    <h4>Apa Kata Mereka?</h4>
                    <img src="./assets/svg/leaf.svg" alt="leaf" loading="lazy">
                </div>
                <p>Kepuasan Pelanggan adalah Prioritas kami</p>

                <div class="row g-3 g-md-4 mt-3 mt-md-4 justify-content-center w-100 text-start">
                    <?php foreach ($reviews as $review): ?>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="review-wrapper">
                                <div class="review d-flex gap-3">
                                    <img src="./assets/svg/quote.svg" alt="quote">
                                    <p>“<?= htmlspecialchars($review['review']) ?>”</p>
                                </div>
        
                                <div class="profile d-flex align-items-center gap-3">
                                    <div class="profile-img"><?= htmlspecialchars($review['initial_name']) ?></div>
                                    <div class="profile-detail">
                                        <h3><?= htmlspecialchars($review['name']) ?></h3>
                                        <div class="rating"><?= renderStars((float) htmlspecialchars($review['rating'])) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>  
                </div>
            </div>
        </div>
    </section>
</main>