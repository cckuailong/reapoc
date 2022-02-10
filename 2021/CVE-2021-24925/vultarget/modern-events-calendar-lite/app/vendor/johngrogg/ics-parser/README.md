# PHP ICS Parser

[![Latest Stable Release](https://poser.pugx.org/johngrogg/ics-parser/v/stable.png "Latest Stable Release")](https://packagist.org/packages/johngrogg/ics-parser)
[![Total Downloads](https://poser.pugx.org/johngrogg/ics-parser/downloads.png "Total Downloads")](https://packagist.org/packages/johngrogg/ics-parser)

---

## Installation

### Requirements
 - PHP 5 (≥ 5.3.0)
 - [Valid ICS](https://icalendar.org/validator.html) (`.ics`, `.ical`, `.ifb`) file
 - [IANA](https://www.iana.org/time-zones) or [Unicode CLDR](http://cldr.unicode.org/translation/timezones) Time Zones

### Setup

 - Install [Composer](https://getcomposer.org/)
   - Add the following dependency to `composer.json`
     - :warning: **Note with Composer the owner is `johngrogg` and not `u01jmg3`**
   - To access the latest stable branch (`v2`) use the following
     - To access new features you can require [`dev-master`](https://getcomposer.org/doc/articles/aliases.md#branch-alias)

       ```yaml
       {
           "require": {
               "johngrogg/ics-parser": "^2"
           }
       }
       ```

## How to use

### How to instantiate the Parser

 - Using the example script as a guide, [refer to this code](https://github.com/u01jmg3/ics-parser/blob/master/examples/index.php#L1-L22)

#### What will the parser return?

 - Each key/value pair from the iCal file will be parsed creating an associative array for both the calendar and every event it contains.
 - Also injected will be content under `dtstart_tz` and `dtend_tz` for accessing start and end dates with time zone data applied.
 - Where possible [`DateTime`](https://secure.php.net/manual/en/class.datetime.php) objects are used and returned.

   ```php
   // Dump the whole calendar
   var_dump($ical->cal);

   // Dump every event
   var_dump($ical->events());
   ```

 - Also included are special `{property}_array` arrays which further resolve the contents of a key/value pair.

   ```php
   // Dump a parsed event's start date
   var_dump($event->dtstart_array);

   // array (size=4)
   //   0 =>
   //     array (size=1)
   //       'TZID' => string 'America/Detroit' (length=15)
   //   1 => string '20160409T090000' (length=15)
   //   2 => int 1460192400
   //   3 => string 'TZID=America/Detroit:20160409T090000' (length=36)
   ```

---

## API

### `ICal` API

#### Variables

| Name                           | Description                                                         | Configurable             | Default Value                                                                             |
|--------------------------------|---------------------------------------------------------------------|:------------------------:|-------------------------------------------------------------------------------------------|
| `$defaultSpan`                 | The value in years to use for indefinite, recurring events          | :ballot_box_with_check:  | `2`                                                                                       |
| `$defaultTimeZone`             | Enables customisation of the default time zone                      | :ballot_box_with_check:  | [System default](https://secure.php.net/manual/en/function.date-default-timezone-get.php) |
| `$defaultWeekStart`            | The two letter representation of the first day of the week          | :ballot_box_with_check:  | `MO`                                                                                      |
| `$disableCharacterReplacement` | Toggles whether to disable all character replacement                | :ballot_box_with_check:  | `false`                                                                                   |
| `$skipRecurrence`              | Toggles whether to skip the parsing of recurrence rules             | :ballot_box_with_check:  | `false`                                                                                   |
| `$useTimeZoneWithRRules`       | Toggles whether to use time zone info when parsing recurrence rules | :ballot_box_with_check:  | `false`                                                                                   |
| `$alarmCount`                  | Tracks the number of alarms in the current iCal feed                | :heavy_multiplication_x: | N/A                                                                                       |
| `$cal`                         | The parsed calendar                                                 | :heavy_multiplication_x: | N/A                                                                                       |
| `$eventCount`                  | Tracks the number of events in the current iCal feed                | :heavy_multiplication_x: | N/A                                                                                       |
| `$freeBusyCount`               | Tracks the free/busy count in the current iCal feed                 | :heavy_multiplication_x: | N/A                                                                                       |
| `$todoCount`                   | Tracks the number of todos in the current iCal feed                 | :heavy_multiplication_x: | N/A                                                                                       |

#### Methods

| Method                                | Parameter(s)                                               | Visibility  | Description                                                                                           |
|---------------------------------------|------------------------------------------------------------|-------------|-------------------------------------------------------------------------------------------------------|
| `__construct`                         | `$files = false`, `$options = array()`                     | `public`    | Creates the ICal object                                                                               |
| `initFile`                            | `$file`                                                    | `protected` | Initialises lines from a file                                                                         |
| `initLines`                           | `$lines`                                                   | `protected` | Initialises the parser using an array containing each line of iCal content                            |
| `initString`                          | `$string`                                                  | `protected` | Initialises lines from a string                                                                       |
| `initUrl`                             | `$url`                                                     | `protected` | Initialises lines from a URL                                                                          |
| `addCalendarComponentWithKeyAndValue` | `$component`, `$keyword`, `$value`                         | `protected` | Add one key and value pair to the `$this->cal` array                                                  |
| `calendarDescription`                 | -                                                          | `public`    | Returns the calendar description                                                                      |
| `calendarName`                        | -                                                          | `public`    | Returns the calendar name                                                                             |
| `calendarTimeZone`                    | `$ignoreUtc`                                               | `public`    | Returns the calendar time zone                                                                        |
| `cleanData`                           | `$data`                                                    | `protected` | Replaces curly quotes and other special characters with their standard equivalents                    |
| `convertDayOrdinalToPositive`         | `$dayNumber`, `$weekday`, `$timestamp`                     | `protected` | Converts a negative day ordinal to its equivalent positive form                                       |
| `eventsFromInterval`                  | `$interval`                                                | `public`    | Returns a sorted array of events following a given string, or `false` if no events exist in the range |
| `eventsFromRange`                     | `$rangeStart = false`, `$rangeEnd = false`                 | `public`    | Returns a sorted array of events in a given range, or an empty array if no events exist in the range  |
| `events`                              | -                                                          | `public`    | Returns an array of Events                                                                            |
| `fileOrUrl`                           | `$filename`                                                | `protected` | Reads an entire file or URL into an array                                                             |
| `freeBusyEvents`                      | -                                                          | `public`    | Returns an array of arrays with all free/busy events                                                  |
| `hasEvents`                           | -                                                          | `public`    | Returns a boolean value whether the current calendar has events or not                                |
| `iCalDateToDateTime`                  | `$icalDate`, `$forceTimeZone = false`, `$forceUtc = false` | `public`    | Returns a `DateTime` object from an iCal date time format                                             |
| `iCalDateToUnixTimestamp`             | `$icalDate`, `$forceTimeZone = false`, `$forceUtc = false` | `public`    | Returns a Unix timestamp from an iCal date time format                                                |
| `iCalDateWithTimeZone`                | `$event`, `$key`, `$format = DATE_TIME_FORMAT`             | `public`    | Returns a date adapted to the calendar time zone depending on the event `TZID`                        |
| `isExdateMatch`                       | `$exdate`, `$anEvent`, `$recurringOffset`                  | `protected` | Checks if an excluded date matches a given date by reconciling time zones                             |
| `isFileOrUrl`                         | `$filename`                                                | `protected` | Checks if a filename exists as a file or URL                                                          |
| `isValidDate`                         | `$value`                                                   | `public`    | Checks if a date string is a valid date                                                               |
| `isValidTimeZoneId`                   | `$timeZone`                                                | `protected` | Checks if a time zone is valid (IANA or CLDR)                                                         |
| `isValidIanaTimeZoneId`               | `$timeZone`                                                | `protected` | Checks if a time zone is a valid IANA time zone                                                       |
| `isValidCldrTimeZoneId`               | `$timeZone`, `doConversion = false`                        | `protected` | Checks if a time zone is a valid CLDR time zone                                                       |
| `keyValueFromString`                  | `$text`                                                    | `protected` | Gets the key value pair from an iCal string                                                           |
| `mb_chr`                              | `$code`                                                    | `protected` | Provides a polyfill for PHP 7.2's `mb_chr()`, which is a multibyte safe version of `chr()`            |
| `mb_str_replace`                      | `$search`, `$replace`, `$subject`, `$count = 0`            | `protected` | Replaces all occurrences of a search string with a given replacement string                           |
| `numberOfDays`                        | `$days`, `$start`, `$end`                                  | `protected` | Gets the number of days between a start and end date                                                  |
| `parseDuration`                       | `$date`, `$duration`, `$format = 'U'`                      | `protected` | Parses a duration and applies it to a date                                                            |
| `parseExdates`                        | `$event`                                                   | `public`    | Parses a list of excluded dates to be applied to an Event                                             |
| `processDateConversions`              | -                                                          | `protected` | Processes date conversions using the time zone                                                        |
| `processEventIcalDateTime`            | `$event`, `$index = 3`                                     | `protected` | Extends the `{DTSTART\|DTEND\|RECURRENCE-ID}_array` array to include an iCal date time for each event |
| `processEvents`                       | -                                                          | `protected` | Performs admin tasks on all events as read from the iCal file                                         |
| `processRecurrences`                  | -                                                          | `protected` | Processes recurrence rules                                                                            |
| `removeUnprintableChars`              | `$data`                                                    | `protected` | Removes unprintable ASCII and UTF-8 characters                                                        |
| `sortEventsWithOrder`                 | `$events`, `$sortOrder = SORT_ASC`                         | `public`    | Sorts events based on a given sort order                                                              |
| `trimToRecurrenceCount`               | `$rrules`, `$recurrenceEvents`                             | `protected` | Ensures the recurrence count is enforced against generated recurrence events                          |
| `unfold`                              | `$lines`                                                   | `protected` | Unfolds an iCal file in preparation for parsing                                                       |

#### Constants

| Name                      | Description                                   |
|---------------------------|-----------------------------------------------|
| `DATE_TIME_FORMAT_PRETTY` | Default pretty date time format to use        |
| `DATE_TIME_FORMAT`        | Default date time format to use               |
| `ICAL_DATE_TIME_TEMPLATE` | String template to generate an iCal date time |
| `RECURRENCE_EVENT`        | Used to isolate generated recurrence events   |
| `SECONDS_IN_A_WEEK`       | The number of seconds in a week               |
| `TIME_FORMAT`             | Default time format to use                    |
| `TIME_ZONE_UTC`           | UTC time zone string                          |
| `UNIX_FORMAT`             | Unix timestamp date format                    |
| `UNIX_MIN_YEAR`           | The year Unix time began                      |

---

### `Event` API (extends `ICal` API)

#### Methods

| Method        | Parameter(s)                                | Visibility  | Description                                                         |
|---------------|---------------------------------------------|-------------|---------------------------------------------------------------------|
| `__construct` | `$data = array()`                           | `public`    | Creates the Event object                                            |
| `prepareData` | `$value`                                    | `protected` | Prepares the data for output                                        |
| `printData`   | `$html = HTML_TEMPLATE`                     | `public`    | Returns Event data excluding anything blank within an HTML template |
| `snakeCase`   | `$input`, `$glue = '_'`, `$separator = '-'` | `protected` | Converts the given input to snake_case                              |

#### Constants

| Name            | Description                                         |
|-----------------|-----------------------------------------------------|
| `HTML_TEMPLATE` | String template to use when pretty printing content |

---

## Credits
 - [Jonathan Goode](https://github.com/u01jmg3) (programming, bug fixing, enhancement, coding standard)
 - [John Grogg](john.grogg@gmail.com) (programming, addition of event recurrence handling)

---

## Tools for Testing

 - [iCal Validator](https://icalendar.org/validator.html)
 - [Recurrence Rule Tester](https://jakubroztocil.github.io/rrule/)
 - [Unix Timestamp Converter](https://www.unixtimestamp.com)
