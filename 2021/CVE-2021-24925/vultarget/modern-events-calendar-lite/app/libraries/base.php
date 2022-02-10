<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Base class.
 * @author Webnus <info@webnus.biz>
 * @abstract
 */
abstract class MEC_base extends MEC
{
    /**
     * Returns MEC_db instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_db instance
     */
	final public function getDB()
    {
        return MEC::getInstance('app.libraries.db');
    }
    
    /**
     * Returns MEC_request instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_request instance
     */
    final public function getRequest()
    {
        return MEC::getInstance('app.libraries.request');
    }
    
    /**
     * Returns MEC_file instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_file instance
     */
    final public function getFile()
    {
        return MEC::getInstance('app.libraries.filesystem', 'MEC_file');
    }
    
    /**
     * Returns MEC_folder instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_folder instance
     */
    final public function getFolder()
    {
        return MEC::getInstance('app.libraries.filesystem', 'MEC_folder');
    }
    
    /**
     * Returns MEC_path instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_path instance
     */
    final public function getPath()
    {
        return MEC::getInstance('app.libraries.filesystem', 'MEC_path');
    }
    
    /**
     * Returns MEC_main instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_main instance
     */
    final public function getMain()
    {
        return MEC::getInstance('app.libraries.main');
    }
    
    /**
     * Returns MEC_factory instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_factory instance
     */
    final public function getFactory()
    {
        return MEC::getInstance('app.libraries.factory');
    }
    
    /**
     * Returns MEC_render instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_render instance
     */
    final public function getRender()
    {
        return MEC::getInstance('app.libraries.render');
    }
    
    /**
     * Returns MEC_parser instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_parser instance
     */
    final public function getParser()
    {
        return MEC::getInstance('app.libraries.parser');
    }
    
    /**
     * Returns MEC_feed instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_feed instance
     */
    final public function getFeed()
    {
        return MEC::getInstance('app.libraries.feed');
    }
    
    /**
     * Returns MEC_book instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_book instance
     */
    final public function getBook()
    {
        return MEC::getInstance('app.libraries.book');
    }
    
    /**
     * Returns MEC_notifications instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_notifications instance
     */
    final public function getNotifications()
    {
        return MEC::getInstance('app.libraries.notifications');
    }

    /**
     * Returns MEC_envato instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_envato instance
     */
    final public function getEnvato()
    {
        return MEC::getInstance('app.libraries.envato');
    }

    /**
     * Returns QRCode instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return QRcode instance
     */
    final public function getQRcode()
    {
        self::import('app.libraries.qrcode');
        return new QRcode();
    }

    /**
     * Returns PRO instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_pro instance
     */
    final public function getPRO()
    {
        return MEC::getInstance(base64_decode('YXBwLmxpYnJhcmllcy5wcm8='));
    }

    /**
     * Returns PRO instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_schedule instance
     */
    final public function getSchedule()
    {
        return MEC::getInstance('app.libraries.schedule');
    }

    /**
     * Returns PRO instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_syncSchedule instance
     */
    final public function getSyncSchedule()
    {
        return MEC::getInstance('app.libraries.syncSchedule');
    }

    /**
     * Returns Cache instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_cache instance
     */
    final public function getCache()
    {
        MEC::import('app.libraries.cache');
        return MEC_cache::getInstance();
    }

    /**
     * Returns WC instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_wc instance
     */
    final public function getWC()
    {
        return MEC::getInstance('app.libraries.wc');
    }

    /**
     * Returns User instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_user instance
     */
    final public function getUser()
    {
        return MEC::getInstance('app.libraries.user');
    }

    /**
     * Returns Hourly Schedule instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_hourlyschedule instance
     */
    final public function getHourlySchedule()
    {
        return MEC::getInstance('app.libraries.hourlyschedule');
    }

    /**
     * Returns Event Fields instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_eventFields instance
     */
    final public function getEventFields()
    {
        return MEC::getInstance('app.libraries.eventFields');
    }

    /**
     * Returns Search instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_search instance
     */
    final public function getSearch()
    {
        return MEC::getInstance('app.libraries.search');
    }

    /**
     * Returns Ticket Variations instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_ticketVariations instance
     */
    final public function getTicketVariations()
    {
        return MEC::getInstance('app.libraries.ticketVariations');
    }

    /**
     * Returns Booking Record instance
     * @final
     * @author Webnus <info@webnus.biz>
     * @return MEC_bookingRecord instance
     */
    final public function getBookingRecord()
    {
        return MEC::getInstance('app.libraries.bookingRecord');
    }
}