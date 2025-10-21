<?php 
// Set the page title before including header
$page_title = 'Administration Office';
require_once 'header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section position-relative text-white">
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background-image: url('images/cover-page.png'); 
                background-size: cover; 
                background-position: center;
                background-repeat: no-repeat;
                opacity: 1.8;">
    </div>
    <div class="container position-relative h-100 d-flex flex-column justify-content-center align-items-center text-center">
        <img src="images/sjcsi-logo.png" alt="School logo" class="mb-4 rounded-circle shadow" style="width: 240px; height: 240px;">
        <h1 class="display-4 fw-bold mb-4" style="color: #094b3d;">ADMINISTRATION OFFICE</h1>
        <p class="lead text-white" style="color: #094b3de6 !important; max-width: 800px;">
            Leading with vision and excellence to ensure the smooth operation and continuous growth of our institution.
        </p>
    </div>
</section>

<!-- Decorative Section -->
<section class="py-4 bg-primary-custom"></section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Left Column - Content -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h2 class="card-title h4 mb-0">Office Overview</h2>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            The Administration Office serves as the central hub for institutional leadership, 
                            strategic planning, and overall management of Saint Joseph College of Sindangan Incorporated. 
                            We are committed to providing visionary leadership and effective administration that supports 
                            the college's mission of delivering quality education.
                        </p>
                        
                        <p class="card-text">
                            Our office oversees all administrative functions, coordinates between departments, 
                            and ensures that institutional policies are implemented effectively. We work closely 
                            with faculty, staff, students, and stakeholders to maintain excellence in all aspects 
                            of college operations.
                        </p>
                        
                        <div class="mt-4 p-3 bg-light rounded">
                            <h3 class="h5 text-primary">LEADERSHIP & EXCELLENCE</h3>
                            <h4 class="h6 text-success">STRATEGIC VISION</h4>
                            <h4 class="h6 text-info">OPERATIONAL EFFICIENCY</h4>
                        </div>
                    </div>
                </div>
                
                <!-- Administrative Structure -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h2 class="card-title h4 mb-0">Administrative Structure</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="h5 text-primary">Executive Leadership</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">College President</li>
                                    <li class="list-group-item">Vice President for Academic Affairs</li>
                                    <li class="list-group-item">Vice President for Administration</li>
                                    <li class="list-group-item">Vice President for Finance</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="h5 text-primary">Administrative Divisions</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Human Resources</li>
                                    <li class="list-group-item">General Services</li>
                                    <li class="list-group-item">Planning & Development</li>
                                    <li class="list-group-item">Quality Assurance</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Leadership Team -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h2 class="card-title h4 mb-0">Administrative Leadership</h2>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="h5 text-primary">Dr. Roberto Santos</h3>
                            <p class="text-muted">College President</p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Position</th>
                                        <th>Name</th>
                                        <th>Responsibilities</th>
                                        <th>Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Vice President, Academic Affairs</td>
                                        <td>Dr. Maria Lopez</td>
                                        <td>Academic Programs, Faculty Development</td>
                                        <td>mlopez@sjcsi.edu.ph</td>
                                    </tr>
                                    <tr>
                                        <td>Vice President, Administration</td>
                                        <td>Mr. Antonio Reyes</td>
                                        <td>Operations, Facilities, HR</td>
                                        <td>areyes@sjcsi.edu.ph</td>
                                    </tr>
                                    <tr>
                                        <td>Vice President, Finance</td>
                                        <td>Ms. Cristina Tan</td>
                                        <td>Budget, Financial Management</td>
                                        <td>ctan@sjcsi.edu.ph</td>
                                    </tr>
                                    <tr>
                                        <td>Executive Assistant</td>
                                        <td>Mrs. Sofia Martinez</td>
                                        <td>Executive Support, Scheduling</td>
                                        <td>smartinez@sjcsi.edu.ph</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Info Panel -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="card-title h5 mb-0">Office Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-clock text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Office Hours</h4>
                                <p class="mb-0">7:30 AM - 5:30 PM</p>
                                <small class="text-muted">Monday to Friday</small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Location</h4>
                                <p class="mb-0">Main Administration Building, 2nd Floor</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-phone text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Main Line</h4>
                                <p class="mb-0">(065) 123-ADMIN</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-phone-alt text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Executive Line</h4>
                                <p class="mb-0">(065) 123-EXEC</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Email</h4>
                                <p class="mb-0">administration@sjcsi.edu.ph</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="card-title h5 mb-0">Upcoming Events</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Board Meeting
                                <span class="badge bg-primary rounded-pill">15th Monthly</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Strategic Planning
                                <span class="badge bg-success rounded-pill">Quarterly</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Faculty Assembly
                                <span class="badge bg-info rounded-pill">Monthly</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Accreditation Review
                                <span class="badge bg-warning rounded-pill">Ongoing</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="card-title h5 mb-0">Administrative Services</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-tie me-2 text-primary"></i>Executive Appointments
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-contract me-2 text-primary"></i>Policy Inquiries
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-handshake me-2 text-primary"></i>Partnership Proposals
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-building me-2 text-primary"></i>Facility Requests
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-users me-2 text-primary"></i>Staff Concerns
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Strategic Priorities Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Strategic Priorities</h2>
            <p class="text-muted">Guiding our institution toward excellence and innovation</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line text-primary mb-3 fs-1"></i>
                        <h3>Institutional Development</h3>
                        <p class="text-muted">Strategic planning and continuous improvement of college programs and facilities.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-graduation-cap text-success mb-3 fs-1"></i>
                        <h3>Academic Excellence</h3>
                        <p class="text-muted">Enhancing educational quality, curriculum development, and faculty advancement.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-hands-helping text-info mb-3 fs-1"></i>
                        <h3>Community Engagement</h3>
                        <p class="text-muted">Building strong partnerships with local communities and industry stakeholders.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Statement -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="card-title h4 mb-0">Administrative Mission</h3>
                    </div>
                    <div class="card-body text-center">
                        <p class="lead mb-0">
                            "To provide visionary leadership and effective administration that fosters academic excellence, 
                            operational efficiency, and sustainable growth, ensuring Saint Joseph College of Sindangan Incorporated 
                            remains a beacon of quality education and positive community impact."
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .bg-primary-custom {
        background-color: #094b3d;
    }
    
    .hero-section {
        min-height: 60vh;
        display: flex;
        align-items: center;
    }
    
    .table th {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .list-group-item {
        border: none;
        padding: 0.75rem 0;
    }
    
    .card-border-primary {
        border-color: var(--primary-color);
    }
</style>

<?php require_once 'footer.php'; ?>