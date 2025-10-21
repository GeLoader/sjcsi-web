<?php  
require_once 'config.php';
require_once BASE_PATH . '/database.php';
 
// Fetch academic calendar events from database
$academicEvents = [];
try {
    $eventsResult = dbQuery("SELECT * FROM academic_calendar ORDER BY start_date ASC");
    while ($row = $eventsResult->fetch_assoc()) {
        $startDate = $row['start_date'];
        $endDate = $row['end_date'];
        
        // Create event object in the format expected by the JavaScript
        $academicEvents[$startDate] = [
            'title' => $row['title'],
            'type' => $row['type'],
            'endDate' => $endDate ?: null,
            'description' => $row['description'] ?: null
        ];
        
        // If this is a multi-day event, add entries for each day
        if ($endDate && $endDate !== $startDate) {
            $currentDate = date('Y-m-d', strtotime($startDate . ' +1 day'));
            $endDateTime = strtotime($endDate);
            
            while (strtotime($currentDate) <= $endDateTime) {
                $academicEvents[$currentDate] = [
                    'title' => $row['title'],
                    'type' => $row['type'],
                    'endDate' => $endDate,
                    'description' => $row['description'] ?: null
                ];
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        }
    }
} catch (Exception $e) {
    error_log("Academic Calendar Error: " . $e->getMessage());
    // Fallback to empty events array if database query fails
    $academicEvents = [];
}

// Fetch academic programs from database
$collegePrograms = [];
$shsPrograms = [];
$jhsPrograms = [];

try {
    // Fetch college programs
    $collegeResult = dbQuery("
        SELECT ap.*, d.name as dept_name 
        FROM academic_programs ap 
        LEFT JOIN departments d ON ap.department_code = d.code 
        WHERE ap.level = 'college' 
        ORDER BY ap.department_code, ap.name
    ");
    while ($row = $collegeResult->fetch_assoc()) {
        $collegePrograms[] = $row;
    }
    
    // Fetch SHS programs
    $shsResult = dbQuery("
        SELECT ap.*, d.name as dept_name 
        FROM academic_programs ap 
        LEFT JOIN departments d ON ap.department_code = d.code 
        WHERE ap.level = 'shs' 
        ORDER BY ap.name
    ");
    while ($row = $shsResult->fetch_assoc()) {
        $shsPrograms[] = $row;
    }
    
    // Fetch JHS programs
    $jhsResult = dbQuery("
        SELECT ap.*, d.name as dept_name 
        FROM academic_programs ap 
        LEFT JOIN departments d ON ap.department_code = d.code 
        WHERE ap.level = 'jhs' 
        ORDER BY ap.name
    ");
    while ($row = $jhsResult->fetch_assoc()) {
        $jhsPrograms[] = $row;
    }
} catch (Exception $e) {
    error_log("Academic Programs Error: " . $e->getMessage());
    // Fallback to empty arrays if database query fails
    $collegePrograms = [];
    $shsPrograms = [];
    $jhsPrograms = [];
}

// Convert PHP array to JavaScript format
$academicEventsJson = json_encode($academicEvents);
?>
<?php include 'header.php'; ?>

<!-- Main Content -->
<div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="fw-bold primary-color mb-3">Academic Programs</h1>
        <p class="lead">
            Discover our comprehensive range of academic programs designed to prepare you for success in your chosen
            career path.
        </p>
    </div>

    <!-- Programs Tabs -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="programTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active tab-hover" style="color:black;" id="college-tab" data-bs-toggle="tab" data-bs-target="#college" type="button">College</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-hover" style="color:black;" id="shs-tab" data-bs-toggle="tab" data-bs-target="#shs" type="button">Senior High</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-hover" style="color:black;" id="jhs-tab" data-bs-toggle="tab" data-bs-target="#jhs" type="button">Junior High</button>
        </li>
    </ul>

    <div class="tab-content" id="programTabsContent">
        <!-- College Programs -->
        <div class="tab-pane fade show active" id="college" role="tabpanel">

            
            <!-- Department Tabs -->
            <ul class="nav nav-pills justify-content-center mb-4" id="collegeDeptTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="caste-tab" data-bs-toggle="pill" data-bs-target="#caste" type="button">CASTE</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cit-tab" data-bs-toggle="pill" data-bs-target="#cit" type="button">CIT</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="coa-tab" data-bs-toggle="pill" data-bs-target="#coa" type="button">COA</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cba-tab" data-bs-toggle="pill" data-bs-target="#cba" type="button">CBA</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cje-tab" data-bs-toggle="pill" data-bs-target="#cje" type="button">CJE</button>
                </li>
 
            </ul>

            <div class="tab-content" id="collegeDeptContent">
                <!-- CASTE Programs -->
                <div class="tab-pane fade show active" id="caste" role="tabpanel">
                    <div class="row g-4">
                        <?php
                        if (empty($collegePrograms)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">No college programs available at this time.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($collegePrograms as $program): ?>
                                <?php if ($program['department_code'] === 'CASTE'): ?>
                                <div class="col-md-6">
                                    <div class="card program-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                            </div>
                                            <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <?php if (!empty($program['duration'])): ?>
                                                        <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($program['units'])): ?>
                                                        <p class="mb-1"><i class="fas fa-book-open text-muted me-2"></i> <?php echo htmlspecialchars($program['units']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <?php if (!empty($program['tuition_fee'])): ?>
                                                        <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                           <?php if (!empty($program['learn_more_link'])): ?>
                                        <?php 
                                        // Check if the link is a PDF file
                                        $isPdf = pathinfo($program['learn_more_link'], PATHINFO_EXTENSION) === 'pdf';
                                        $target = $isPdf ? '_blank' : '_self';
                                        ?>
                                        <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" 
                                           class="btn btn-primary w-100 mt-3" 
                                           target="<?php echo $target; ?>"
                                           <?php if ($isPdf): ?>rel="noopener noreferrer"<?php endif; ?>>
                                            Learn More
                                        </a>
                                    <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CIT Programs -->
                <div class="tab-pane fade" id="cit" role="tabpanel">
                    <div class="row g-4">
                        <?php
                        if (empty($collegePrograms)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">No CIT programs available at this time.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($collegePrograms as $program): ?>
                                <?php if ($program['department_code'] === 'CIT'): ?>
                                <div class="col-md-6">
                                    <div class="card program-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                            </div>
                                            <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <?php if (!empty($program['duration'])): ?>
                                                        <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($program['units'])): ?>
                                                        <p class="mb-1"><i class="fas fa-book-open text-muted me-2"></i> <?php echo htmlspecialchars($program['units']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <?php if (!empty($program['tuition_fee'])): ?>
                                                        <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($program['learn_more_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- COA Programs -->
                <div class="tab-pane fade" id="coa" role="tabpanel">
                    <div class="row g-4">
                        <?php
                        if (empty($collegePrograms)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">No COA programs available at this time.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($collegePrograms as $program): ?>
                                <?php if ($program['department_code'] === 'COA'): ?>
                                <div class="col-md-6">
                                    <div class="card program-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                            </div>
                                            <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <?php if (!empty($program['duration'])): ?>
                                                        <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($program['units'])): ?>
                                                        <p class="mb-1"><i class="fas fa-book-open text-muted me-2"></i> <?php echo htmlspecialchars($program['units']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <?php if (!empty($program['tuition_fee'])): ?>
                                                        <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($program['learn_more_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CBA Programs -->
                <div class="tab-pane fade" id="cba" role="tabpanel">
                    <div class="row g-4">
                        <?php
                        if (empty($collegePrograms)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">No CBA programs available at this time.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($collegePrograms as $program): ?>
                                <?php if ($program['department_code'] === 'CBA'): ?>
                                <div class="col-md-6">
                                    <div class="card program-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                            </div>
                                            <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <?php if (!empty($program['duration'])): ?>
                                                        <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($program['units'])): ?>
                                                        <p class="mb-1"><i class="fas fa-book-open text-muted me-2"></i> <?php echo htmlspecialchars($program['units']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <?php if (!empty($program['tuition_fee'])): ?>
                                                        <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($program['learn_more_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CJE Programs -->
                <div class="tab-pane fade" id="cje" role="tabpanel">
                    <div class="row g-4">
                        <?php
                        if (empty($collegePrograms)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">No CJE programs available at this time.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($collegePrograms as $program): ?>
                                <?php if ($program['department_code'] === 'CJE'): ?>
                                <div class="col-md-6">
                                    <div class="card program-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                            </div>
                                            <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <?php if (!empty($program['duration'])): ?>
                                                        <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($program['units'])): ?>
                                                        <p class="mb-1"><i class="fas fa-book-open text-muted me-2"></i> <?php echo htmlspecialchars($program['units']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <?php if (!empty($program['tuition_fee'])): ?>
                                                        <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($program['learn_more_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SHS Department -->
                <div class="tab-pane fade" id="shs-dept" role="tabpanel">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold primary-color">Senior High School Programs</h2>
                        <p class="text-muted">Our Senior High School program offers various academic tracks to prepare students for college and career paths.</p>
                    </div>
                    
                    <div class="row g-4">
                        <?php
                        if (empty($shsPrograms)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">No SHS programs available at this time.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($shsPrograms as $program): ?>
                                <div class="col-md-6">
                                    <div class="card program-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                            </div>
                                            <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <?php if (!empty($program['duration'])): ?>
                                                        <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($program['units'])): ?>
                                                        <p class="mb-1"><i class="fas fa-book-open text-muted me-2"></i> <?php echo htmlspecialchars($program['units']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <?php if (!empty($program['tuition_fee'])): ?>
                                                        <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($program['learn_more_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- JHS Department -->
                <div class="tab-pane fade" id="jhs-dept" role="tabpanel">
                    <div class="row g-4">
                        <?php
                        if (empty($jhsPrograms)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">No JHS programs available at this time.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($jhsPrograms as $program): ?>
                                <div class="col-md-6">
                                    <div class="card program-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                            </div>
                                            <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <?php if (!empty($program['duration'])): ?>
                                                        <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <?php if (!empty($program['tuition_fee'])): ?>
                                                        <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($program['learn_more_link'])): ?>
                                                <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SHS Programs -->
        <div class="tab-pane fade" id="shs" role="tabpanel">
            <div class="text-center mb-5">
                <h2 class="fw-bold primary-color">Senior High School Programs</h2>
                <p class="text-muted">Our Senior High School program offers various academic tracks to prepare students for college and career paths.</p>
            </div>
            
            <div class="row g-4">
                <?php
                if (empty($shsPrograms)): ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No SHS programs available at this time.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($shsPrograms as $program): ?>
                        <div class="col-md-6">
                            <div class="card program-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                    </div>
                                    <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <?php if (!empty($program['duration'])): ?>
                                                <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($program['units'])): ?>
                                                <p class="mb-1"><i class="fas fa-book-open text-muted me-2"></i> <?php echo htmlspecialchars($program['units']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-6">
                                            <?php if (!empty($program['tuition_fee'])): ?>
                                                <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($program['learn_more_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- JHS Programs -->
        <div class="tab-pane fade" id="jhs" role="tabpanel">
            <div class="row g-4">
                <?php
                if (empty($jhsPrograms)): ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No JHS programs available at this time.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jhsPrograms as $program): ?>
                        <div class="col-md-6">
                            <div class="card program-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title h5"><?php echo htmlspecialchars($program['name']); ?></h3>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($program['dept_name']); ?></span>
                                    </div>
                                    <p class="card-text text-muted mt-2"><?php echo htmlspecialchars($program['description']); ?></p>
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <?php if (!empty($program['duration'])): ?>
                                                <p class="mb-1"><i class="far fa-clock text-muted me-2"></i> <?php echo htmlspecialchars($program['duration']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-6">
                                            <?php if (!empty($program['tuition_fee'])): ?>
                                                <p class="mb-1"><i class="fas fa-money-bill-wave text-muted me-2"></i> <?php echo htmlspecialchars($program['tuition_fee']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($program['learn_more_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($program['learn_more_link']); ?>" class="btn btn-primary w-100 mt-3">Learn More</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<!-- Calendar of Activities -->
<section class="my-5 py-5 bg-light rounded">
    <div class="text-center mb-5">
        <h2 class="fw-bold primary-color"><i class="far fa-calendar-alt me-2"></i>Academic Calendar</h2>
        <p class="text-muted">Important dates and activities displayed in a monthly calendar view</p>
        <?php if (empty($academicEvents)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No academic calendar events are currently available. Please check back later or contact the administration.
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($academicEvents)): ?>
    <div class="calendar-container">
        <!-- Calendar Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button class="btn btn-outline-primary" id="prevMonth"><i class="fas fa-chevron-left"></i></button>
            <h3 class="mb-0 primary-color" id="currentMonthYear">Loading...</h3>
            <button class="btn btn-outline-primary" id="nextMonth"><i class="fas fa-chevron-right"></i></button>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid bg-white rounded shadow-sm" id="calendarGrid">
            <!-- Calendar will be populated by JavaScript -->
        </div>

        <!-- Calendar Legend -->
        <div class="mt-4">
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <div class="d-flex align-items-center">
                    <div class="legend-color bg-primary me-2" style="width: 15px; height: 15px;"></div>
                    <small>Enrollment</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color bg-success me-2" style="width: 15px; height: 15px;"></div>
                    <small>Classes Start/End</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color bg-warning me-2" style="width: 15px; height: 15px;"></div>
                    <small>Examinations</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color bg-danger me-2" style="width: 15px; height: 15px;"></div>
                    <small>Holidays</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color bg-info me-2" style="width: 15px; height: 15px;"></div>
                    <small>Events</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="legend-color bg-secondary me-2" style="width: 15px; height: 15px;"></div>
                    <small>Breaks</small>
                </div>
            </div>
        </div>

        <!-- Event Details Modal -->
        <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="eventDetailsContent">
                        <!-- Event details will be populated here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

  <!-- Admission Requirements Section -->
<section id="admission" class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Admission Requirements</h2>
        
        <ul class="nav nav-pills mb-4 justify-content-center" id="admissionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="college-req-tab" data-bs-toggle="pill" data-bs-target="#college-req" type="button">College</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shs-req-tab" data-bs-toggle="pill" data-bs-target="#shs-req" type="button">Senior High School</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="jhs-req-tab" data-bs-toggle="pill" data-bs-target="#jhs-req" type="button">Junior High School</button>
            </li>
        </ul>
        
        <div class="tab-content" id="admissionTabsContent">
            <!-- College Requirements -->
            <div class="tab-pane fade show active" id="college-req" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $collegeReqs = [];
                            try {
                                $collegeReqsResult = dbQuery("SELECT * FROM admission_requirements WHERE level = 'college' ORDER BY display_order ASC");
                                while ($row = $collegeReqsResult->fetch_assoc()) {
                                    $collegeReqs[] = $row;
                                }
                            } catch (Exception $e) {
                                error_log("College Requirements Error: " . $e->getMessage());
                            }
                            
                            if (empty($collegeReqs)): ?>
                                <li class="list-group-item">No requirements available at this time.</li>
                            <?php else: ?>
                                <?php foreach ($collegeReqs as $req): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($req['requirement']); ?></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- SHS Requirements -->
            <div class="tab-pane fade" id="shs-req" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $shsReqs = [];
                            try {
                                $shsReqsResult = dbQuery("SELECT * FROM admission_requirements WHERE level = 'shs' ORDER BY display_order ASC");
                                while ($row = $shsReqsResult->fetch_assoc()) {
                                    $shsReqs[] = $row;
                                }
                            } catch (Exception $e) {
                                error_log("SHS Requirements Error: " . $e->getMessage());
                            }
                            
                            if (empty($shsReqs)): ?>
                                <li class="list-group-item">No requirements available at this time.</li>
                            <?php else: ?>
                                <?php foreach ($shsReqs as $req): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($req['requirement']); ?></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- JHS Requirements -->
            <div class="tab-pane fade" id="jhs-req" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $jhsReqs = [];
                            try {
                                $jhsReqsResult = dbQuery("SELECT * FROM admission_requirements WHERE level = 'jhs' ORDER BY display_order ASC");
                                while ($row = $jhsReqsResult->fetch_assoc()) {
                                    $jhsReqs[] = $row;
                                }
                            } catch (Exception $e) {
                                error_log("JHS Requirements Error: " . $e->getMessage());
                            }
                            
                            if (empty($jhsReqs)): ?>
                                <li class="list-group-item">No requirements available at this time.</li>
                            <?php else: ?>
                                <?php foreach ($jhsReqs as $req): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($req['requirement']); ?></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enrollment Process Section -->
<section id="enrollment" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Enrollment Process</h2>
        
        <ul class="nav nav-pills mb-4 justify-content-center" id="enrollmentProcessTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="college-process-tab" data-bs-toggle="pill" data-bs-target="#college-process-front" type="button">College</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="shs-process-tab" data-bs-toggle="pill" data-bs-target="#shs-process-front" type="button">Senior High</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="jhs-process-tab" data-bs-toggle="pill" data-bs-target="#jhs-process-front" type="button">Junior High</button>
            </li>
        </ul>

        <div class="tab-content" id="enrollmentProcessTabsContent">
            <!-- College Enrollment Process -->
            <div class="tab-pane fade show active" id="college-process-front" role="tabpanel">
                <div class="row">
                    <?php
                    $collegeProcess = [];
                    try {
                        $collegeProcessResult = dbQuery("SELECT * FROM enrollment_process WHERE level = 'college' ORDER BY step_number ASC");
                        while ($row = $collegeProcessResult->fetch_assoc()) {
                            $collegeProcess[] = $row;
                        }
                    } catch (Exception $e) {
                        error_log("College Process Error: " . $e->getMessage());
                    }
                    
                    if (empty($collegeProcess)): ?>
                        <div class="col-12 text-center">
                            <p>No enrollment process information available for College at this time.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($collegeProcess as $step): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="icon-rounded mb-3 <?php echo $step['color_class']; ?>">
                                            <i class="<?php echo $step['icon_class']; ?> text-white"></i>
                                        </div>
                                        <h4 class="h5">Step <?php echo $step['step_number']; ?>: <?php echo htmlspecialchars($step['title']); ?></h4>
                                        <p class="card-text"><?php echo htmlspecialchars($step['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SHS Enrollment Process -->
            <div class="tab-pane fade" id="shs-process-front" role="tabpanel">
                <div class="row">
                    <?php
                    $shsProcess = [];
                    try {
                        $shsProcessResult = dbQuery("SELECT * FROM enrollment_process WHERE level = 'shs' ORDER BY step_number ASC");
                        while ($row = $shsProcessResult->fetch_assoc()) {
                            $shsProcess[] = $row;
                        }
                    } catch (Exception $e) {
                        error_log("SHS Process Error: " . $e->getMessage());
                    }
                    
                    if (empty($shsProcess)): ?>
                        <div class="col-12 text-center">
                            <p>No enrollment process information available for Senior High School at this time.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($shsProcess as $step): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="icon-rounded mb-3 <?php echo $step['color_class']; ?>">
                                            <i class="<?php echo $step['icon_class']; ?> text-white"></i>
                                        </div>
                                        <h4 class="h5">Step <?php echo $step['step_number']; ?>: <?php echo htmlspecialchars($step['title']); ?></h4>
                                        <p class="card-text"><?php echo htmlspecialchars($step['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- JHS Enrollment Process -->
            <div class="tab-pane fade" id="jhs-process-front" role="tabpanel">
                <div class="row">
                    <?php
                    $jhsProcess = [];
                    try {
                        $jhsProcessResult = dbQuery("SELECT * FROM enrollment_process WHERE level = 'jhs' ORDER BY step_number ASC");
                        while ($row = $jhsProcessResult->fetch_assoc()) {
                            $jhsProcess[] = $row;
                        }
                    } catch (Exception $e) {
                        error_log("JHS Process Error: " . $e->getMessage());
                    }
                    
                    if (empty($jhsProcess)): ?>
                        <div class="col-12 text-center">
                            <p>No enrollment process information available for Junior High School at this time.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($jhsProcess as $step): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="icon-rounded mb-3 <?php echo $step['color_class']; ?>">
                                            <i class="<?php echo $step['icon_class']; ?> text-white"></i>
                                        </div>
                                        <h4 class="h5">Step <?php echo $step['step_number']; ?>: <?php echo htmlspecialchars($step['title']); ?></h4>
                                        <p class="card-text"><?php echo htmlspecialchars($step['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

</div>

<style>
    .calendar-month {
        height: 100%;
    }
    
    .calendar-event {
        padding: 8px 12px;
        margin-bottom: 10px;
        border-radius: 4px;
    }
    
    .event-date {
        font-weight: bold;
        font-size: 0.9rem;
    }
    
    .event-desc {
        font-size: 0.85rem;
    }
    
    .primary-color {
        color: #0d6efd;
    }
    
    /* Tab hover effects */
    .tab-hover:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    
    .req-tab-hover:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    
    /* Calendar styles */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background-color: #dee2e6;
        border: 1px solid #dee2e6;
    }
    
 .calendar-day {
    background-color: white;
    min-height: 120px; /* Increased height for better visibility */
    padding: 10px; /* Increased padding */
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease; /* Smooth transition for hover effect */
}
    
 .calendar-day:hover {
    background-color: #f0f8ff; /* Light blue background on hover */
    transform: scale(1.05); /* Slight zoom effect */
    z-index: 10; /* Ensure hovered element appears above others */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow for depth */
}

    
    .calendar-day-header {
        background-color: #f8f9fa !important;
        font-weight: bold;
        padding: 10px 8px;
        text-align: center;
        border-bottom: 2px solid #dee2e6;
    }
    
   .day-number {
    font-weight: bold;
    margin-bottom: 8px;
    font-size: 1.1rem; /* Increased font size for date numbers */
}

.calendar-event-indicator {
    width: 12px; /* Larger indicators */
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
    margin-bottom: 4px;
}

    
   .event-tooltip {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(51, 51, 51, 0.95);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    white-space: nowrap;
    z-index: 100;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s;
    font-size: 0.9rem; /* Slightly larger font */
    font-weight: 500;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
    
.calendar-day:hover .event-tooltip {
    opacity: 1;
    visibility: visible;
}
    
    .other-month {
        background-color: #f8f9fa;
        color: #6c757d;
    }
    
    .today {
        background-color: #e7f4ff;
    }

  .event-indicators {
    margin-top: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
/* Enhanced modal styling */
.modal-body .card-title {
    font-size: 1.1rem;
    margin-bottom: 10px;
}

.modal-body .card-text {
    font-size: 1rem;
    line-height: 1.5;
}
    .has-events {
        border-left: 3px solid #0d6efd;
    }

    /* Responsive adjustments */
@media (max-width: 768px) {
    .calendar-day {
        min-height: 80px; /* Slightly smaller on mobile */
        padding: 5px;
    }
    
    .day-number {
        font-size: 0.9rem;
    }
    
    .calendar-event-indicator {
        width: 8px;
        height: 8px;
    }
}
</style>

<script>
    // Load academic events from PHP
    const academicEvents = <?php echo $academicEventsJson; ?>;

    // Function to generate calendar
   function generateCalendar(year, month) {
        const calendarGrid = document.getElementById('calendarGrid');
        
        // Check if calendar container exists
        if (!calendarGrid) {
            console.log('Calendar container not found - events may be empty');
            return;
        }
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        // Update month and year display
        document.getElementById('currentMonthYear').textContent = `${monthNames[month]} ${year}`;
        
        // Clear previous calendar
        calendarGrid.innerHTML = '';
        
        // Add day headers
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        days.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();
        
        // Add empty cells for days before the first day of the month
        for (let i = firstDay - 1; i >= 0; i--) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            const prevDay = prevMonthDays - i;
            emptyDay.innerHTML = `<div class="day-number">${prevDay}</div>`;
            calendarGrid.appendChild(emptyDay);
        }
        
        // Add days of the month
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            
            // Check if today
            if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                dayElement.classList.add('today');
            }
            
            // Day number
            const dayNumber = document.createElement('div');
            dayNumber.className = 'day-number';
            dayNumber.textContent = day;
            dayElement.appendChild(dayNumber);
            
            // Check for events
            const eventsForDay = [];
            if (academicEvents[dateStr]) {
                eventsForDay.push(academicEvents[dateStr]);
            }
            
            // Add event indicators and titles
            if (eventsForDay.length > 0) {
                dayElement.classList.add('has-events');
                
                // Add event indicators
                const eventIndicators = document.createElement('div');
                eventIndicators.className = 'event-indicators';
                
                eventsForDay.forEach(event => {
                    const indicator = document.createElement('span');
                    indicator.className = 'calendar-event-indicator';
                    
                    switch (event.type) {
                        case 'enrollment':
                            indicator.style.backgroundColor = '#0d6efd';
                            break;
                        case 'classes':
                            indicator.style.backgroundColor = '#198754';
                            break;
                        case 'exams':
                            indicator.style.backgroundColor = '#ffc107';
                            break;
                        case 'holiday':
                            indicator.style.backgroundColor = '#dc3545';
                            break;
                        case 'event':
                            indicator.style.backgroundColor = '#0dcaf0';
                            break;
                        case 'break':
                            indicator.style.backgroundColor = '#6c757d';
                            break;
                        default:
                            indicator.style.backgroundColor = '#6c757d';
                    }
                    
                    eventIndicators.appendChild(indicator);
                });
                
                dayElement.appendChild(eventIndicators);
                
                // Add event titles (displaying the first event title)
                if (eventsForDay[0].title) {
                    const eventTitle = document.createElement('span');
                    eventTitle.className = 'event-title';
                    eventTitle.textContent = eventsForDay[0].title;
                    eventTitle.title = eventsForDay[0].title; // Add tooltip for full title
                    dayElement.appendChild(eventTitle);
                }
                
                // Add click event for event details
                dayElement.addEventListener('click', function() {
                    showEventDetails(dateStr, eventsForDay);
                });
                
                // Add tooltip for additional info
                const tooltip = document.createElement('div');
                tooltip.className = 'event-tooltip';
                tooltip.textContent = `Click for ${eventsForDay.length > 1 ? `${eventsForDay.length} events` : 'event details'}`;
                dayElement.appendChild(tooltip);
            }
            
            calendarGrid.appendChild(dayElement);
        }

        // Add remaining days from next month to fill the grid
        const remainingCells = 42 - (firstDay + daysInMonth); // 6 weeks * 7 days = 42 cells
        for (let day = 1; day <= remainingCells && remainingCells < 7; day++) {
            const nextMonthDay = document.createElement('div');
            nextMonthDay.className = 'calendar-day other-month';
            nextMonthDay.innerHTML = `<div class="day-number">${day}</div>`;
            calendarGrid.appendChild(nextMonthDay);
        }
    }
    
    // Function to show event details in modal
    function showEventDetails(dateStr, events) {
        const modalTitle = document.getElementById('eventDetailsModalLabel');
        const modalContent = document.getElementById('eventDetailsContent');
        
        const date = new Date(dateStr);
        const formattedDate = date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        modalTitle.textContent = `Events on ${formattedDate}`;
        
        let content = '';
        events.forEach(event => {
            const typeClass = getEventTypeClass(event.type);
            content += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <span class="badge ${typeClass} me-2">${event.type.charAt(0).toUpperCase() + event.type.slice(1)}</span>
                            ${event.title}
                        </h6>
                        ${event.description ? `<p class="card-text">${event.description}</p>` : ''}
                        ${event.endDate && event.endDate !== dateStr ? 
                            `<small class="text-muted">Until: ${new Date(event.endDate).toLocaleDateString()}</small>` : 
                            ''
                        }
                    </div>
                </div>
            `;
        });
        
        modalContent.innerHTML = content;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
        modal.show();
    }
    
    // Function to get event type class
    function getEventTypeClass(type) {
        switch (type) {
            case 'enrollment': return 'bg-primary';
            case 'classes': return 'bg-success';
            case 'exams': return 'bg-warning';
            case 'holiday': return 'bg-danger';
            case 'event': return 'bg-info';
            case 'break': return 'bg-secondary';
            default: return 'bg-secondary';
        }
    }
    
    // Initialize calendar if events exist
    if (Object.keys(academicEvents).length > 0) {
        let currentDate = new Date();
        let currentYear = currentDate.getFullYear();
        let currentMonth = currentDate.getMonth();
        
        // Generate initial calendar
        generateCalendar(currentYear, currentMonth);
        
        // Navigation buttons
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentYear, currentMonth);
        });
        
        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentYear, currentMonth);
        });
    }
</script>

            <style>
                /* Custom styling for department tabs */
                .nav-pills .nav-link {
                    color: #000 !important; /* Black font for inactive tabs */
                    background-color: #f8f9fa; /* Light background for inactive tabs */
                    margin: 0 5px;
                    border-radius: 20px;
                    padding: 8px 20px;
                    transition: all 0.3s ease;
                }
                
                .nav-pills .nav-link:hover {
                    background-color: #e9ecef;
                    color: #000 !important;
                }
                
                .nav-pills .nav-link.active {
                    background-color: #094b3d !important; /* Dark green background for active tab */
                    color: #fff !important; /* White font for active tab */
                    font-weight: 600;
                }
                .icon-rounded {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-purple {
    background-color: #6f42c1 !important;
}
            </style>

<?php include 'footer.php'; ?>