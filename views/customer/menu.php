<main class="main-menu">
    <section class="container-fluid m-0 p-0" id="hero-menus-section">
        <div class="hero-menu row mx-0 align-items-center justify-content-center overflow-hidden">
            <div class="hero-menu-content d-flex flex-column ">
                <p class="fw-semibold">MENU CATERING HARIAN</p>
                <h1 class="fw-bold m-0">Pilihan Menu</h1>
                <h1 class="fw-bold">Sehat & Lezat</h1>
                <p>
                    Nikmati berbagai menu rumahan yang dibuat dengan
                    bahan berkualitas dan penuh cita rasa. Cocok untuk
                    menemani aktivitas harianmu!
                </p>
            </div>
        </div>
    </section>

    <section class="container-fluid py-5" id="daily-menus-section">
        <div class="daily-menus align-items-center">
            <!-- dmc-1 -->
            <div class="daily-menus-content dmc-1 row d-flex m-0 mb-3 p-0" id="header-container">
                <div class="col-md d-flex flex-column align-items-md-start align-items-center mb-2 mb-md-0">
                    <h3 class="fw-semibold m-0 p-0 text-center text-md-left" id="header-title">Menu Hari ini</h3>
                    <p class="m-0 text-center text-md-left" id="header-subtitle">Menu yang tersedia untuk hari ini</p>
                </div>

                <div class="col-md d-flex justify-content-md-end justify-content-center align-items-center">
                    <span class="badge-date text-center">
                        <i class="bi bi-calendar-week pe-2"></i><span id="header-date"><?= htmlspecialchars($tanggalHariIni); ?></span>
                    </span>
                </div>
            </div>

            <!-- dmc-2 -->
            <div class="daily-menus-content dmc-2 row d-flex flex-md-row align-items-center justify-content-center p-2 mb-4">
                <div class="col d-flex justify-content-center">
                    <button type="button" class="btn menu-mode-btn active p-2" id="btn-mode-daily" onclick="switchMode('daily')">
                        <i class="bi bi-calendar-week pe-2"></i>Menu Hari ini
                    </button>
                </div>

                <div class="col d-none d-md-flex justify-content-center">
                    <button type="button" class="btn menu-mode-btn non-active p-2" id="btn-mode-weekly" onclick="switchMode('weekly')">
                        <i class="bi bi-calendar-week pe-2"></i>Menu Minggu ini
                    </button>
                </div>

                <!-- weekly mobile (dropdown) -->
                <div class="col d-flex d-md-none justify-content-center dropdown">
                    <button class="btn menu-mode-btn non-active p-2 dropdown-toggle" type="button" id="btn-mode-weekly-mobile" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <i class="bi bi-calendar-week pe-2"></i><span id="mobile-dropdown-label">Menu Minggu ini</span>
                    </button>
                    <ul class="dropdown-menu dm-center flex-column justify-content-center shadow border-0" aria-labelledby="btn-mode-weekly-mobile">
                        <?php 
                        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        foreach ($days as $day): 
                        ?>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="selectWeeklyDay('<?= $day ?>', true)"><?= $day ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- weekly dekstop (radio) -->
            <div id="weekly-selector-container" class="d-none my-4">
                <div class="d-none d-md-flex justify-content-center gap-2 flex-wrap">
                    <?php foreach ($days as $index => $day): ?>
                        <input type="radio" class="btn-check" name="weekly-day-radio" id="day-<?= $day ?>" value="<?= $day ?>" <?= $index === 0 ? 'checked' : '' ?> onchange="selectWeeklyDay(this.value, false)">
                        <label class="btn fw-medium btn-outline-success px-3 py-2 rounded-pill" for="day-<?= $day ?>"><?= $day ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- dmc-3 atau card di render lewat js -->
            <div class="daily-menus-content dmc-3 row g-md-5 g-4 flex-nowrap overflow-auto pb-3" id="menu-cards-container">
                
            </div>
        </div>
    </section>


<!-- Mengirim variabel dari render ke JS -->
    <script>
        const dailyData = <?= json_encode($dailyMenus); ?>;
        const weeklyData = <?= json_encode($weeklyMenus); ?>;
        const todayFormatted = <?= json_encode($tanggalHariIni); ?>;
    </script>
</main>