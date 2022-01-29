<?php

/**
 * This class represents the Notes model of the plugin
 * 
 * This class is data entity for the notes section.It contains the data for notes.
 * and some basic database operations for this model.
 *
 * @author CMSHelplive.
 */
class RM_Notes extends RM_Base_Model
{

    public $note_id;
    public $submission_id;
    public $notes;
//    public $type;
    public $status;
    public $publication_date;
    public $published_by;
    public $last_edit_date;
    public $last_edited_by;
    public $note_options;
    public $valid_options;

    public function __construct()
    {
        $this->initialized = false;
        $this->note_id = NULL;
        $this->status = 'draft';
        $this->valid_options = array('bg_color','type');
        $this->note_options = new stdClass;
        foreach ($this->valid_options as $valid_option)
            $this->note_options->$valid_option = null;
    }

    public function get_note_id()
    {
        return $this->note_id;
    }

    public function get_submission_id()
    {
        return $this->submission_id;
    }

    public function get_notes()
    {
        return $this->notes;
    }

    /*    public function get_type()
      {
      return $this->type;
      } */

    public function get_published_by()
    {
        return $this->published_by;
    }
    
    public function get_status()
    {
        return $this->status;
    }

    public function get_publication_date()
    {
        return $this->publication_date;
    }

    public function get_last_edit_date()
    {
        return $this->last_edit_date;
    }

    public function get_last_edited_by()
    {
        return $this->last_edited_by;
    }

    public function get_note_options()
    {
        return maybe_unserialize($this->note_options);
    }
    
    public function get_note_type() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_model = new RM_Notes_Addon();
            return $addon_model->get_note_type($this);
        }
    }
    
    public function set_type($type) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_model = new RM_Notes_Addon();
            return $addon_model->set_type($type,$this);
        }
    }
    
    public function set_initialized($status) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_model = new RM_Notes_Addon();
            return $addon_model->set_initialized($status,$this);
        }
    }

    public function set_note_id($note_id)
    {
        $this->note_id = $note_id;
    }

    public function set_submission_id($submission_id)
    {
        $this->submission_id = $submission_id;
    }

    public function set_notes($notes)
    {
        $this->notes = $notes;
    }

//    public function set_type($type)
//    {
//        $this->type = $type;
//    }

    public function set(array $request)
    {
        foreach ($request as $property => $value)
        {
            if (property_exists($this, $property))
            {
                $set_property_method = 'set_' . $property;
                $this->$set_property_method($value);
            } elseif (in_array($property, $this->valid_options, true))
            {
                $this->note_options->$property = $value;
            }
        }

        return $this->initialized = true;
    }

    public function set_status($status)
    {
        if (is_array($status) && isset($status[0]))
        {
            if ($status[0] == 1)
            {
                $this->status = 'publish';
            } else
                $this->status = 'draft';
        }else if ($status == 'publish' || $status = 'draft')
            $this->status = $status;
    }

    public function set_publication_date($publication_date)
    {
        $this->publication_date = $publication_date;
    }

    public function set_last_edit_date($last_edit_date)
    {
        $this->last_edit_date = $last_edit_date;
    }

    public function set_last_edited_by($last_edited_by)
    {
        $this->last_edited_by = $last_edited_by;
    }

    public function set_note_options($note_option)
    {
        $this->note_option = $note_option;
    }

    public function set_published_by($published_by)
    {
        $this->published_by = $published_by;
    }
    /* 'note_id' => $this->note_id,
      'submission_id' => $this->submission_id,
      'notes' => $this->notes,
      'userid' => $this->userid,
      'useremail' => $this->useremail,
      'type' => $this->type,
      'status' => $this->status,
      'publish_date' => $this->publish_date,
      'last_edit_date' => $this->last_edit_date,
      'last_edited_by' => $this->last_edited_by,
      'note_options' => $this->note_options,
     */

    public function insert_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }

        if ($this->note_id)
        {
            return false;
        }

        $data = array(
            'submission_id' => $this->submission_id,
            'notes' => $this->notes,
//                   'type' => $this->type,
            'status' => $this->status,
            'publication_date' => RM_Utilities::get_current_time(),
            'published_by' => get_current_user_id(),
            'note_options' => maybe_serialize($this->note_options)
        );

        $data_specifiers = array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
        );

        $result = RM_DBManager::insert_row('NOTES', $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        $this->note_id = $result;

        return $this->note_id;
    }

    public function update_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }
        if (!$this->note_id)
        {
            return false;
        }

        $data = array(
            'submission_id' => $this->submission_id,
            'notes' => $this->notes,
//                  'type' => $this->type,
            'status' => $this->status,
            'last_edit_date' => RM_Utilities::get_current_time(),
            'last_edited_by' => get_current_user_id(),
            'note_options' => maybe_serialize($this->note_options)
        );

        $data_specifiers = array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        );

        $result = RM_DBManager::update_row('NOTES', $this->note_id, $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }


        return true;
    }

    public function load_from_db($note_id, $should_set_id = true)
    {

        $result = RM_DBManager::get_row('NOTES', $note_id);

        if (null !== $result)
        {
            if ($should_set_id)
                $this->note_id = $note_id;
            else
                $this->note_id = null;
            $this->submission_id = $result->submission_id;
            $this->notes = $result->notes;
//                $this->type = $result->type;
            $this->status = $result->status;
            $this->publication_date = $result->publication_date;
            $this->published_by = $result->published_by;
            $this->last_edit_date = $result->last_edit_date;
            $this->last_edited_by = $result->last_edited_by;
            $this->note_options = maybe_unserialize($result->note_options);
        } else
        {
            return false;
        }
        $this->initialized = true;
        return true;
    }

    public function remove_from_db()
    {
        return RM_DBManager::remove_row('NOTES', $this->note_id);
    }

}
