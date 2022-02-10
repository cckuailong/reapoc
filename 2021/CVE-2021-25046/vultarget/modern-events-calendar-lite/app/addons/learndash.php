<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC LearnDash addon class
 * @author Webnus <info@webnus.biz>
 */
class MEC_addon_learndash extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC Factory class
        $this->factory = $this->getFactory();
        
        // MEC Main class
        $this->main = $this->getMain();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }
    
    /**
     * Initialize the LD addon
     * @author Webnus <info@webnus.biz>
     * @return boolean
     */
    public function init()
    {
        // Module is not enabled
        if(!isset($this->settings['ld_status']) or (isset($this->settings['ld_status']) and !$this->settings['ld_status'])) return false;

        // Tickets
        add_action('custom_field_ticket', array($this, 'add_courses_dropdown_to_tickets'), 10, 2);
        add_action('custom_field_dynamic_ticket', array($this, 'add_courses_dropdown_to_raw_tickets'));

        // Add to Course
        add_action('mec_booking_completed', array($this, 'assign'), 10, 1);

        return true;
    }

    public function add_courses_dropdown_to_tickets($ticket, $key)
    {
        // LearnDash is not installed
        if(!defined('LEARNDASH_VERSION')) return;

        $courses = $this->get_courses();
        if(!count($courses)) return;
        ?>
        <div class="mec-form-row">
            <label for="mec_tickets_<?php echo $key; ?>_ld_course"><?php _e('LearnDash Course', 'modern-events-calendar-lite'); ?></label>
            <select name="mec[tickets][<?php echo $key; ?>][ld_course]" id="mec_tickets_<?php echo $key; ?>_ld_course">
                <option>-----</option>
                <?php foreach($courses as $course_id => $course_name): ?>
                <option value="<?php echo esc_attr($course_id); ?>"<?php echo ((isset($ticket['ld_course']) and $course_id == $ticket['ld_course']) ? 'selected="selected"' : ''); ?>><?php echo esc_html($course_name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }

    public function add_courses_dropdown_to_raw_tickets()
    {
        // LearnDash is not installed
        if(!defined('LEARNDASH_VERSION')) return;

        $this->add_courses_dropdown_to_tickets(array(), ':i:');
    }

    public function get_courses()
    {
        $courses = array();

        $posts = get_posts(array('post_type' => 'sfwd-courses', 'posts_per_page' => -1));
        if($posts) foreach($posts as $post) $courses[$post->ID] = $post->post_title;

        return $courses;
    }

    public function assign($book_id)
    {
        // LearnDash is not installed
        if(!defined('LEARNDASH_VERSION')) return;

        $user = $this->getUser()->booking($book_id);

        $event_id = get_post_meta($book_id, 'mec_event_id', true);
        $ticket_ids = explode(',', get_post_meta($book_id, 'mec_ticket_id', true));

        $tickets = get_post_meta($event_id, 'mec_tickets', true);

        $courses = array();
        foreach($tickets as $ticket_id => $ticket)
        {
            if(!is_numeric($ticket_id)) continue;
            if(!in_array($ticket_id, $ticket_ids)) continue;

            $courses[] = $ticket['ld_course'];
        }

        // Associate Courses
        foreach($courses as $course_id)
        {
            ld_update_course_access($user->ID, $course_id, false);
        }
    }
}