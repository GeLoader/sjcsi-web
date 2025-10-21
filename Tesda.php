<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">TESDA Programs</h1>
                <p class="lead mb-4">
                    Technical Education and Skills Development Authority programs at SJCSI - Building skilled professionals
                    for the modern workforce.
                </p>
                <a href="#" class="btn btn-warning btn-lg text-dark fw-bold">Enroll Now</a>
            </div>
            <div class="col-md-6">
                <img src="images/tesda-facility.jpg" alt="TESDA Training Facility" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Available Programs -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Available Programs</h2>
            <p class="text-muted">Choose from our wide range of technical and vocational programs</p>
        </div>

        <div class="row g-4">
            <?php
            $tesdaPrograms = [
                [
                    'title' => "Computer Systems Servicing NC II",
                    'duration' => "320 hours",
                    'description' => "Learn computer hardware troubleshooting, software installation, and basic networking.",
                    'requirements' => ["High School Graduate", "Basic Computer Knowledge"],
                    'certification' => "TESDA NC II Certificate",
                ],
                [
                    'title' => "Food Processing NC II",
                    'duration' => "280 hours",
                    'description' => "Master food preservation, packaging, and quality control techniques.",
                    'requirements' => ["High School Graduate", "Food Safety Training"],
                    'certification' => "TESDA NC II Certificate",
                ],
                [
                    'title' => "Automotive Servicing NC I",
                    'duration' => "360 hours",
                    'description' => "Basic automotive maintenance, repair, and diagnostic skills.",
                    'requirements' => ["High School Graduate", "Physical Fitness"],
                    'certification' => "TESDA NC I Certificate",
                ],
                [
                    'title' => "Electrical Installation and Maintenance NC II",
                    'duration' => "400 hours",
                    'description' => "Electrical wiring, installation, and maintenance of electrical systems.",
                    'requirements' => ["High School Graduate", "Basic Math Skills"],
                    'certification' => "TESDA NC II Certificate",
                ],
                [
                    'title' => "Welding NC I & NC II",
                    'duration' => "480 hours",
                    'description' => "Arc welding, gas welding, and metal fabrication techniques.",
                    'requirements' => ["High School Graduate", "Physical Fitness", "Good Eyesight"],
                    'certification' => "TESDA NC I & NC II Certificate",
                ],
                [
                    'title' => "Digital Marketing",
                    'duration' => "240 hours",
                    'description' => "Social media marketing, content creation, and online advertising strategies.",
                    'requirements' => ["High School Graduate", "Basic Computer Skills"],
                    'certification' => "TESDA Certificate",
                ],
            ];

            foreach ($tesdaPrograms as $program) {
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-secondary">TESDA</span>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php echo $program['duration']; ?>
                                </small>
                            </div>
                            <h3 class="h5 card-title"><?php echo $program['title']; ?></h3>
                            <p class="card-text text-muted"><?php echo $program['description']; ?></p>
                            
                            <div class="mb-3">
                                <h4 class="h6 fw-bold">Requirements:</h4>
                                <ul class="list-unstyled">
                                    <?php foreach ($program['requirements'] as $req) { ?>
                                        <li class="mb-1">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <?php echo $req; ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            
                            <div class="pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold">Certification:</span>
                                    <span class="badge bg-light text-dark border"><?php echo $program['certification']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- About TESDA -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">About TESDA at SJCSI</h2>
            <p class="text-muted mx-auto" style="max-width: 800px;">
                The Technical Education and Skills Development Authority (TESDA) sets direction, promulgates relevant
                standards, and implements programs geared towards quality assured and inclusive technical education and
                skills development. At SJCSI, we offer comprehensive TESDA programs designed to equip students with
                practical skills for immediate employment.
            </p>
        </div>

        <div class="row g-4">
            <?php
            $benefits = [
                [
                    'icon' => 'bi-award',
                    'title' => "Industry-Recognized Certification",
                    'description' => "Receive TESDA certificates that are recognized by employers nationwide",
                ],
                [
                    'icon' => 'bi-people',
                    'title' => "Expert Instructors",
                    'description' => "Learn from certified trainers with extensive industry experience",
                ],
                [
                    'icon' => 'bi-tools',
                    'title' => "Hands-On Training",
                    'description' => "Practical training with modern equipment and real-world scenarios",
                ],
                [
                    'icon' => 'bi-check-circle',
                    'title' => "Job Placement Assistance",
                    'description' => "Career guidance and job placement support after graduation",
                ],
            ];

            foreach ($benefits as $benefit) {
            ?>
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="bg-light text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="<?php echo $benefit['icon']; ?> fs-3"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-2"><?php echo $benefit['title']; ?></h3>
                    <p class="text-muted small"><?php echo $benefit['description']; ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Enrollment Process -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">How to Enroll</h2>
            <p class="text-muted">Simple steps to start your TESDA journey</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <h3 class="h4 fw-bold mb-4">Enrollment Steps</h3>
                <div class="d-flex flex-column gap-3">
                    <?php
                    $enrollmentSteps = [
                        "Visit TESDA Office at SJCSI",
                        "Submit required documents",
                        "Complete application form",
                        "Pay registration fee",
                        "Attend orientation",
                        "Begin classes",
                    ];
                    
                    foreach ($enrollmentSteps as $index => $step) {
                    ?>
                        <div class="d-flex align-items-center gap-3">
                            <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <?php echo $index + 1; ?>
                            </span>
                            <span><?php echo $step; ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="h4 card-title fw-bold">Required Documents</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Birth Certificate (NSO/PSA)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                High School Diploma/Certificate
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                2x2 ID Pictures (4 pieces)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Medical Certificate
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Barangay Clearance
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="h4 card-title fw-bold">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            Class Schedules
                        </h3>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <h4 class="h6 fw-bold mb-1">Morning Classes</h4>
                                <p class="small text-muted">8:00 AM - 12:00 PM (Monday to Friday)</p>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1">Afternoon Classes</h4>
                                <p class="small text-muted">1:00 PM - 5:00 PM (Monday to Friday)</p>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1">Weekend Classes</h4>
                                <p class="small text-muted">8:00 AM - 5:00 PM (Saturday & Sunday)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="h4 card-title fw-bold">Training Fees</h3>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between">
                                <span>Registration Fee</span>
                                <span class="fw-bold">₱500</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Training Fee</span>
                                <span class="fw-bold">₱3,000 - ₱8,000</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Materials Fee</span>
                                <span class="fw-bold">₱1,000 - ₱3,000</span>
                            </div>
                            <div class="pt-2 border-top">
                                <p class="small text-muted">
                                    * Fees vary depending on the program. Scholarships and payment plans available.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Contact TESDA Office</h2>
            <p class="text-muted">Get in touch with our TESDA coordinators for more information</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-4">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-telephone text-primary fs-4"></i>
                                <div>
                                    <h4 class="h5 fw-bold mb-0">Phone</h4>
                                    <p class="text-muted mb-0">(065) 123-4567 ext. 105</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-envelope text-primary fs-4"></i>
                                <div>
                                    <h4 class="h5 fw-bold mb-0">Email</h4>
                                    <p class="text-muted mb-0">tesda@sjcsi.edu.ph</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-clock text-primary fs-4"></i>
                                <div>
                                    <h4 class="h5 fw-bold mb-0">Office Hours</h4>
                                    <p class="text-muted mb-0">Monday - Friday: 8:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-primary btn-lg">Schedule a Visit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>