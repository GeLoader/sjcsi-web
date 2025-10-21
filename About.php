<?php require_once 'header.php'; ?>

<!-- Hero Section -->
<section class="hero-section position-relative text-white" >
        <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background-image: url('images/cover-page.png'); 
                background-size: cover; 
                background-position: center;
                background-repeat: no-repeat;
                opacity: 1.8;">
    </div>
    <!-- <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-20"></div> -->
    <div class="container position-relative h-100 d-flex flex-column justify-content-center align-items-center text-center">
        <img src="images/sjcsi-logo.png" alt="School logo" class="mb-4 rounded-circle shadow" style="width: 240px; height: 240px;">
        <h1 class="display-4 fw-bold mb-4" style="color: #094b3d;">About SJCSI</h1>
        <p class="lead text-white" style="color: #094b3de6 !important; max-width: 800px;">
            For over three decades, Saint Joseph College of Sindangan Incorporated has been a beacon of educational
            excellence, nurturing minds and building futures in the heart of Zamboanga del Norte.
        </p>
    </div>
</section>

<!-- Decorative Section -->
<section class="py-4" style="background-color: #094b3d;"></section>

<!-- Mission, Vision -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Vision Card -->
            <div class="col-lg-6">
                <div class="card h-100 border-start border-success border-4">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-eye text-success me-2 fs-4"></i>
                            <h2 class="card-title h5 mb-0 text-uppercase">Vision</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted">
                            SAINT JOSEPH COLLEGE OF SINDANGAN INCORPORATED aims to be a premier Catholic institution 
                            known for its relevant, quality education in the Philippines and beyond.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mission Card -->
            <div class="col-lg-6">
                <div class="card h-100 border-start border-primary border-4">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bullseye text-primary me-2 fs-4"></i>
                            <h2 class="card-title h5 mb-0 text-uppercase">Mission</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted">
                            SJCSI provides a well-rounded Catholic education that produces graduates who are academically, 
                            technologically, socially, morally, and spiritually well-equipped to respond to the needs of the 
                            local, regional, national, and international or global community.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JOSEPHIAN Core Values -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Our Core Values</h2>
            <p class="text-muted">The JOSEPHIAN values that guide our institution</p>
        </div>

        <div class="row g-4">
            <?php
            $josephianValues = [
                [
                    'letter' => "J",
                    'title' => "Justice",
                    'description' => "Saint Josephians advocate equality and justice for a harmonious life."
                ],
                [
                    'letter' => "O",
                    'title' => "Optimism",
                    'description' => "Saint Josephians look forward to a happier community, a happier nation, a happier World—and always believe they can make it happen!"
                ],
                [
                    'letter' => "S",
                    'title' => "Solidarity",
                    'description' => "Saint Josephians believe in oneness to attain common welfare goals."
                ],
                [
                    'letter' => "E",
                    'title' => "Excellence",
                    'description' => "Saint Josephians aim for the finest...strive for the best."
                ],
                [
                    'letter' => "P",
                    'title' => "Perseverance",
                    'description' => "Saint Josephians are consistent in pursuing the school's goals."
                ],
                [
                    'letter' => "H",
                    'title' => "Honesty",
                    'description' => "Saint Josephians value truth in the pursuit of life's happiness."
                ],
                [
                    'letter' => "I",
                    'title' => "Integrity",
                    'description' => "Saint Josephians uphold uprightness in every action they take."
                ],
                [
                    'letter' => "A",
                    'title' => "Accountability",
                    'description' => "Saint Josephians take responsibility for every assigned task or duty."
                ],
                [
                    'letter' => "N",
                    'title' => "Nobility",
                    'description' => "Saint Josephians place first and foremost the common good in every intention or step they undertake."
                ],
                [
                    'letter' => "S",
                    'title' => "Service",
                    'description' => "Saint Josephians always find time to care for and serve the needy."
                ]
            ];

            foreach ($josephianValues as $value) {
                echo '
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="core-value-letter mb-3 mx-auto d-flex align-items-center justify-content-center">
                                '.$value['letter'].'
                            </div>
                            <h3 class="h5">'.$value['title'].'</h3>
                            <p class="text-muted">'.$value['description'].'</p>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Milestones -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Our Journey</h2>
            <p class="text-muted">Key milestones in our history</p>
        </div>

        <div class="timeline">
            <?php
            $milestones = [
                ['year' => "June 1968", 'event' => "Offering of first- and second-year high school courses"],
                ['year' => "June 1969", 'event' => "Offering of third- and fourth-year high school courses"],
                ['year' => "June 1975", 'event' => "Offering of general clerical course; Saint Joseph High School changed its name to Saint Joseph Institute"],
                ['year' => "June 1977", 'event' => "Offering of commerce and liberal arts courses"],
                ['year' => "March 1978", 'event' => "Change of Saint Joseph Institute name to St. Joseph College, Inc."],
                ['year' => "June 1982", 'event' => "Offering of Bachelor of Science in Secondary Education"],
                ['year' => "June 1988", 'event' => "Offering of Bachelor in Elementary Education"],
                ['year' => "February 2002", 'event' => "Composition of School Hymn: lyrics by Engr. Leonor A. Labadan, and music by Dr. Noel R. Galeza"],
                ['year' => "June 2006", 'event' => "Offering—when Saint Joseph became a partner of TESDA—of TESDA courses, such as the two-year computer hardware servicing, two-year computerized office management, PC operations, and shorter term courses such as caregiving and housekeeping"],
                ['year' => "June 2006", 'event' => "Change of the Commerce curriculum to Business Administration"],
                ['year' => "June 2010", 'event' => "Offering of the four-year Information Technology course"],
                ['year' => "June 2011", 'event' => "Offering of the Accounting Technology course"],
                ['year' => "June 2012", 'event' => "Offering of the Accountancy course"],
                ['year' => "August 2012", 'event' => "Change of name from Saint Joseph College to Saint Joseph College of Sindangan was approved"]
            ];

            foreach ($milestones as $index => $milestone) {
                // Add special circles for specific milestones
                $circleClass = '';
                if ($index == 2 || $index == 7 || $index == 13) {
                    $circleClass = 'timeline-circle';
                }
                
                echo '
                <div class="timeline-item">
                    <div class="timeline-year '.$circleClass.'">'.$milestone['year'].'</div>
                    <div class="timeline-content">
                        <p class="mb-0">'.$milestone['event'].'</p>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- History Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3 text-uppercase">Brief History</h2>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <p class="mb-4">
                            The desire for a quality Christian education in light of the Diocesan thrust for evangelization inspired the Diocese of Dipolog to establish in 1968 Saint Joseph High School, now known as <strong>Saint Joseph College of Sindangan Incorporated</strong>, in Sindangan, Zamboanga del Norte.
                        </p>
                        
                        <p class="mb-4">
                            The late <em>Rev. Fr. Constancio P. Mesiona</em>, then director of Saint Vincent's College in Dipolog City, gathered eight young, intelligent and energetic teachers to start the institution's operation in June 1968, with two-hundred eighty-nine first-year and second-year high school students.
                        </p>
                        
                        <p class="mb-0">
                            Since then, the school has grown to become the college that it is today, and has produced a number of successful graduates in the country.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
      /* Timeline styling */
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
        padding-left: 50px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 25px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--primary-color);
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    
    .timeline-year {
        position: absolute;
        left: -50px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
        text-align: center;
        padding: 2px;
    }
    
    .timeline-circle {
        width: 50px !important;
        height: 50px !important;
        left: -55px !important;
        background: #094b3d !important;
        font-size: 11px !important;
    }
    
    .timeline-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
       .core-value-letter {
        width: 60px;
        height: 60px;
        background-color: #094b3d;
        color: white;
        border-radius: 50%;
        font-size: 24px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<?php require_once 'footer.php'; ?>