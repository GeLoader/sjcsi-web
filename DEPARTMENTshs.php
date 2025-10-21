<?php
// Dynamic DEPARTMENTSHS.php - Pulls content from database
$page_title = 'SHS Department';
require_once 'config.php';
require_once BASE_PATH . '/database.php';
require_once 'header.php';

// Get all sections from database
$sections_query = dbPrepare("SELECT * FROM SHS_page WHERE is_active = 1 ORDER BY display_order");
$sections_query->execute();
$sections_result = $sections_query->get_result();
$sections = [];
while ($row = $sections_result->fetch_assoc()) {
    $sections[$row['section_key']] = $row;
}

// Get faculty data
$faculty_query = dbPrepare("SELECT * FROM SHS_faculty WHERE is_active = 1 ORDER BY display_order");
$faculty_query->execute();
$faculty_result = $faculty_query->get_result();
$faculty = $faculty_result->fetch_all(MYSQLI_ASSOC);

// Helper function to get section content
function getSection($sections, $key, $field = 'content') {
    return isset($sections[$key]) ? htmlspecialchars($sections[$key][$field]) : '';
}

// Helper function to get section title
function getSectionTitle($sections, $key) {
    return getSection($sections, $key, 'title');
}

// Helper function to get section image
function getSectionImage($sections, $key) {
    return isset($sections[$key]['image_url']) ? htmlspecialchars($sections[$key]['image_url']) : '';
}

// Helper function to decode JSON metadata
function getSectionMeta($sections, $key) {
    if (!isset($sections[$key]['meta_data'])) return null;
    return json_decode($sections[$key]['meta_data'], true);
}
?>

<!-- Hero Section -->
<section class="hero-section position-relative text-white">
    <?php
    $hero_meta = getSectionMeta($sections, 'hero_title');
    $bg_image = getSectionImage($sections, 'hero_title') ?: ($hero_meta['bg_image'] ?? 'images/SHS-cover.jpg');
    $logo_image = $hero_meta['logo_image'] ?? 'images/sjcsi-logo.png';
    ?>
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background-image: url('<?= $bg_image ?>'); 
                background-size: cover; 
                background-position: center;
                background-repeat: no-repeat;
                opacity: 0.8;">
    </div>
    <div class="container position-relative h-100 d-flex flex-column justify-content-center align-items-center text-center">
        <img src="<?= $logo_image ?>" alt="School logo" class="mb-4 rounded-circle shadow" style="width: 240px; height: 240px;">
        <h1 class="display-4 fw-bold mb-4" style="color: #094b3d;"><?= getSectionTitle($sections, 'hero_title') ?></h1>
        <p class="lead text-white" style="color: #094b3de6 !important; max-width: 800px;">
            <?= getSection($sections, 'hero_title') ?>
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
                <!-- Department Overview -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h2 class="card-title h4 mb-0"><?= getSectionTitle($sections, 'dept_overview') ?></h2>
                    </div>
                    <div class="card-body">
                        <?php if ($image = getSectionImage($sections, 'dept_overview')): ?>
                            <img src="<?= $image ?>" alt="Department Overview" class="img-fluid mb-3" style="max-width: 100%;">
                        <?php endif; ?>
                        <?php 
                        $overview_content = getSection($sections, 'dept_overview');
                        $paragraphs = explode('\n\n', $overview_content);
                        foreach ($paragraphs as $paragraph): 
                            if (trim($paragraph)):
                        ?>
                        <p class="card-text"><?= nl2br(trim($paragraph)) ?></p>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                        
                    </div>
                </div>
                
                <!-- Program Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h2 class="card-title h4 mb-0"><?= getSectionTitle($sections, 'academic_programs') ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="h5 text-primary">Undergraduate Programs</h4>
                                <ul class="list-group list-group-flush">
                                    <?php 
                                    $programs_meta = getSectionMeta($sections, 'academic_programs');
                                    if ($programs_meta && isset($programs_meta['programs'])):
                                        foreach ($programs_meta['programs'] as $program):
                                            if (!empty($program['name'])):
                                    ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars($program['name']) ?></strong>
                                        <?php if (!empty($program['description'])): ?>
                                            <p class="mb-0 small"><?= htmlspecialchars($program['description']) ?></p>
                                        <?php endif; ?>
                                    </li>
                                    <?php 
                                            endif;
                                        endforeach;
                                    endif;
                                    
                                    if (empty($programs_meta['programs']) || count(array_filter($programs_meta['programs'], function($program) {
                                        return !empty($program['name']);
                                    })) === 0):
                                    ?>
                                    <li class="list-group-item text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No programs configured
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                             
                        </div>
                    </div>
                </div>
                
                <!-- Faculty Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h2 class="card-title h4 mb-0"><?= getSectionTitle($sections, 'faculty_info') ?></h2>
                    </div>
                    <div class="card-body">
                        <?php 
                        // Get chairperson from faculty table
                        $chairperson_query = dbPrepare("SELECT * FROM SHS_faculty WHERE is_chairperson = 1 AND is_active = 1 LIMIT 1");
                        $chairperson_query->execute();
                        $chairperson_result = $chairperson_query->get_result();
                        $chairperson = $chairperson_result->fetch_assoc();
                        
                        if ($chairperson):
                        ?>
         
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Faculty/Staff Member</th>
                                        <th>Specialization/Position</th>
                                        <th>Contact</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $counter = 1;
                                    foreach ($faculty as $member): 
                                    ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td>
                                            <?= htmlspecialchars($member['name']) ?>
                                            <?php if ($member['is_chairperson']): ?>
                                                <span class="badge bg-primary ms-1">Head</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($member['position']) ?><br>
                                            <small><?= htmlspecialchars($member['specialization'] ?? '') ?></small></td>
                                        <td><?= htmlspecialchars($member['email'] ?? '') ?><br>
                                            <small><?= htmlspecialchars($member['phone'] ?? '') ?></small></td>
                                        <td>
                                            <?php if ($member['profile_image']): ?>
                                                <img src="<?= htmlspecialchars($member['profile_image']) ?>" alt="Profile" style="max-width: 50px;">
                                            <?php else: ?>
                                                No image
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
               
            </div>
            
            <!-- Right Column - Info Panel -->
            <div class="col-lg-4">

                <!-- Upcoming Events -->
                <div class="card mb-4">
                  <div class="card-header bg-white">
                    <h3 class="card-title h5 mb-0"><?= getSectionTitle($sections, 'upcoming_events') ?></h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php 
                        $events_meta = getSectionMeta($sections, 'upcoming_events');
                        if ($events_meta && isset($events_meta['events'])):
                            foreach ($events_meta['events'] as $event):
                                if (!empty($event['name']) && !empty($event['date'])):
                        ?>
                        <div class="list-group-item">
                            <h5 class="h6 mb-1"><?= htmlspecialchars($event['name']) ?></h5>
                            <p class="mb-0 small"><?= htmlspecialchars($event['date']) ?></p>
                        </div>
                        <?php 
                                endif;
                            endforeach;
                        endif;
                        
                        if (empty($events_meta['events']) || count(array_filter($events_meta['events'], function($event) {
                            return !empty($event['name']) && !empty($event['date']);
                        })) === 0):
                        ?>
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-calendar fa-2x mb-2"></i>
                            <p>No upcoming events scheduled</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                </div>

                <!-- Contact Button Card -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
            <h5 class="card-title">Contact <?php echo $page_title; ?></h5>
            <p class="card-text">Have questions? Get in touch with us</p>
            <button type="button" class="btn btn-primary-custom" data-toggle="modal" data-target="#contactModal">
                <i class="fas fa-paper-plane me-2"></i>Send Message
            </button>
        </div>
    </div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary-custom text-white">
                <h5 class="modal-title" id="contactModalLabel">Contact <?php echo $page_title; ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="contact-form" method="POST">
                    <input type="hidden" name="department" value="SHS">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Your Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Contact Number *</label>
                                <input type="number" class="form-control" id="contact_no" name="contact_no" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                    </div>
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <div id="form-message" class="alert" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="contact-form" class="btn btn-primary-custom">
                    <span class="submit-text">Send Message</span>
                    <span class="loading-spinner" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
      
       <!-- Department Information -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="card-title h5 mb-0"><?= getSectionTitle($sections, 'contact_info') ?></h3>
                    </div>
                    <div class="card-body">
                        <?php 
                        $contact_meta = getSectionMeta($sections, 'contact_info');
                        if ($contact_meta):
                        ?>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-clock text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Office Hours</h4>
                                <p class="mb-0"><?= htmlspecialchars($contact_meta['office_hours'] ?? '') ?></p>
                                <small class="text-muted"><?= htmlspecialchars($contact_meta['days'] ?? '') ?></small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Location</h4>
                                <p class="mb-0"><?= htmlspecialchars($contact_meta['location'] ?? '') ?></p>
                            </div>
                        </div>
                  <div class="d-flex align-items-center">
                            <i class="fas fa-envelope text-primary me-3 fs-5"></i>
                            <div>
                                <h4 class="h6 mb-0">Email</h4>
                                <p class="mb-0"><?= htmlspecialchars($contact_meta['email'] ?? '') ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                          
            </div>
        </div>
    </div>
</section>

 <script>
$(document).ready(function() {
    $('#contact-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        $('#form-message').hide().removeClass('alert-success alert-danger');
        
        $.ajax({
            url: 'send_message.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#form-message').addClass('alert-success').html(response.message).show();
                    $('#contact-form')[0].reset();
                } else {
                    $('#form-message').addClass('alert-danger').html(response.message).show();
                }
            },
            error: function() {
                $('#form-message').addClass('alert-danger').html('An error occurred while sending your message. Please try again.').show();
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Send Message');
            }
        });
    });
});
</script>

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
</style>

<?php require_once 'footer.php'; ?>