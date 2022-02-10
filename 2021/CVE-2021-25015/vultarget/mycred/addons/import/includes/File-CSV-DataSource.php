<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
if ( !defined( 'myCRED_VERSION' ) ) exit;
/**
 * CSV Import Class
 *
 * LICENSE: The MIT License
 *
 * Copyright (c) <2008> <Kazuyoshi Tlacaelel>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Kazuyoshi Tlacaelel <kazu.dev@gmail.com>
 * @edited    Gabriel Sebastian Merovingi
 * @copyright 2008 Kazuyoshi Tlacaelel
 * @license   The MIT License
 * @version   1.0.1
 * @link      http://code.google.com/p/php-csv-parser/
 */
if ( !class_exists( 'File_CSV_DataSource' ) ) {
	class File_CSV_DataSource {

		/**
		 * CSV Parsing Default-settings
		 * @var array
		 * @access public
		 */
		public $settings = array(
			'delimiter' => ',',
			'eol'   		=> ";",
			'length'		=> 999999,
			'escape'		=> '"'
		);

		/**
		 * Imported Data from CSV
		 * @var array
		 * @access protected
		 */
		protected $rows = array();

		/**
		 * CSV File to parse
		 * @var string
		 * @access protected
		 */
		protected $_filename = '';

		/**
		 * CSV Headers to parse
		 * @var array
		 * @access protected
		 */
		protected $headers = array();

		/**
		 * Data Load Initialize
		 * @param mixed $filename please look at the load() method
		 * @access public
		 * @see load()
		 * @return void
		 */
		public function __construct( $filename = null ) {
			$this->load( $filename );
		}

		/**
		 * CSV File Loader
		 * Indicates the object which file is to be loaded
		 *
		 * @param string $filename the csv filename to load
		 * @access public
		 * @return boolean true if file was loaded successfully
		 * @see isSymmetric(), getAsymmetricRows(), symmetrize()
		 */
		public function load( $filename ) {
			$this->_filename = $filename;
			$this->flush();
			return $this->parse();
		}

		/**
		 * Settings Alterator
		 * Lets you define different settings for scanning given array will override the internal settings
		 *
		 * @param mixed $array containing settings to use
		 * @access public
		 * @return boolean true if changes where applyed successfully
		 * @see $settings
		 */
		public function settings( $array ) {
			$this->settings = array_merge( $this->settings, $array );
		}

		/**
		 * Header Fetcher
		 * Gets csv headers into an array
		 *
		 * @access public
		 * @return array
		 */
		public function getHeaders() {
			return $this->headers;
		}

		/**
		 * Header Counter
		 * Retrives the total number of loaded headers
		 *
		 * @access public
		 * @return integer gets the length of headers
		 */
		public function countHeaders() {
			return count( $this->headers );
		}

		/**
		 * Header and Row Relationship Builder
		 * Attempts to create a relationship for every single cell that was captured and its corresponding header.
		 * The sample below shows how a connection/relationship is built.
		 *
		 * @param array $columns the columns to connect, if nothing is given all headers will be used to create a connection
		 * @access public
		 * @return array If the data is not symmetric an empty array will be returned instead
		 * @see isSymmetric(), getAsymmetricRows(), symmetrize(), getHeaders()
		 */
		public function connect( $columns = array() ) {
			if ( !$this->isSymmetric() ) return array();

			if ( !is_array( $columns ) ) return array();

			if ( $columns === array() ) $columns = $this->headers;

			$ret_arr = array();

			foreach ( $this->rows as $record ) {
				$item_array = array();
				foreach ( $record as $column => $value ) {
					$header = $this->headers[$column];
					if ( in_array( $header, $columns ) ) {
						$item_array[$header] = $value;
					}
				}

				// do not append empty results
				if ( $item_array !== array() )
					array_push( $ret_arr, $item_array );
			}

			return $ret_arr;
		}

		/**
		 * Data Length/Symmetry Checker
		 * Tells if the headers and all of the contents length match.
		 *
		 * Note: there is a lot of methods that won't work if data is not symmetric this method is very important!
		 *
		 * @access public
		 * @return boolean
		 * @see symmetrize(), getAsymmetricRows(), isSymmetric()
		 */
		public function isSymmetric() {
			$hc = count( $this->headers );
			foreach ( $this->rows as $row ) {
				if ( count( $row ) != $hc ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Asymmetric Data Fetcher
		 * Finds the rows that do not match the headers length lets assume that we add one more row to our csv file.
		 * that has only two values. Something like
		 *
		 * @access public
		 * @return array filled with rows that do not match headers
		 * @see getHeaders(), symmetrize(), isSymmetric(),
		 * getAsymmetricRows()
		 */
		public function getAsymmetricRows() {
			$ret_arr = array();
			$hc = count( $this->headers );
			foreach ( $this->rows as $row ) {
				if ( count( $row ) != $hc ) {
					$ret_arr[] = $row;
				}
			}
			return $ret_arr;
		}

		/**
		 * All Rows Length Equalizer
		 * Makes the length of all rows and headers the same. If no $value is given
		 * all unexistent cells will be filled with empty spaces
		 *
		 * @param mixed $value the value to fill the unexistent cells
		 * @access public
		 * @return array
		 * @see isSymmetric(), getAsymmetricRows(), symmetrize()
		 */
		public function symmetrize( $value = '' ) {
			$max_length = 0;
			$headers_length = count( $this->headers );

			foreach ( $this->rows as $row ) {
				$row_length = count( $row );
				if ( $max_length < $row_length ) {
					$max_length = $row_length;
				}
			}

			if ( $max_length < $headers_length ) $max_length = $headers_length;

			foreach ( $this->rows as $key => $row ) {
				$this->rows[$key] = array_pad( $row, $max_length, $value );
			}

			$this->headers = array_pad( $this->headers, $max_length, $value );
		}

		/**
		 * Grid Walker
		 * Travels through the whole dataset executing a callback per each cell
		 *
		 * Note: callback functions get the value of the cell as an argument, and whatever that
		 * callback returns will be used to replace the current value of that cell.
		 *
		 * @param string $callback the callback function to be called per each cell in the dataset.
		 * @access public
		 * @return void
		 * @see walkColumn(), walkRow(), fillColumn(), fillRow(), fillCell()
		 */
		public function walkGrid( $callback ) {
			foreach ( array_keys( $this->getRows() ) as $key ) {
				if ( !$this->walkRow( $key, $callback ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Column Fetcher
		 * Gets all the data for a specific column identified by $name
		 *
		 * Note! $name is the same as the items returned by getHeaders()
		 *
		 * @param string $name the name of the column to fetch
		 * @access public
		 * @return array filled with values of a column
		 * @see getHeaders(), fillColumn(), appendColumn(), getCell(), getRows(),
		 * getRow(), hasColumn()
		 */
		public function getColumn( $name ) {
			if ( !in_array( $name, $this->headers ) ) return array();

			$ret_arr = array();
			$key = array_search( $name, $this->headers, true );
			foreach ( $this->rows as $data ) {
				$ret_arr[] = $data[$key];
			}
			return $ret_arr;
		}

		/**
		 * Column Existance Checker
		 * Checks if a column exists, columns are identified by their header name.
		 *
		 * @param string $string an item returned by getHeaders()
		 * @access public
		 * @return boolean
		 * @see getHeaders()
		 */
		public function hasColumn( $string ) {
			return in_array( $string, $this->headers );
		}

		/**
		 * Column Appender
		 * Appends a column and each or all values in it can be dinamically filled. Only when the $values argument is given.
		 *
		 * @param string $column an item returned by getHeaders()
		 * @param mixed  $values same as fillColumn()
		 * @access public
		 * @return boolean
		 * @see getHeaders(), fillColumn(), fillCell(), createHeaders(),
		 * setHeaders()
		 */
		public function appendColumn( $column, $values = null ) {
			if ( $this->hasColumn( $column ) ) return false;

			$this->headers[] = $column;
			$length = $this->countHeaders();
			$rows = array();

			foreach ( $this->rows as $row ) {
				$rows[] = array_pad( $row, $length, '' );
			}

			$this->rows = $rows;

			if ( $values === null ) $values = '';

			return $this->fillColumn( $column, $values );
		}

		/**
 		 * Collumn Data Injector
 		 * Fills alll the data in the given column with $values
 		 *
 		 * @param mixed $column the column identified by a string
 		 * @param mixed $values ither one of the following
 		 *  - (Number) will fill the whole column with the value of number
 		 *  - (String) will fill the whole column with the value of string
 		 *  - (Array) will fill the while column with the values of array
 		 *		the array gets ignored if it does not match the length of rows
 		 *
 		 * @access public
 		 * @return void
 		 */
		public function fillColumn( $column, $values = null ) {
			if ( !$this->hasColumn( $column ) ) return false;

			if ( $values === null ) return false;

			if ( !$this->isSymmetric() ) return false;

			$y = array_search( $column, $this->headers );

			if ( is_numeric( $values ) || is_string( $values ) ) {
				foreach ( range( 0, $this->countRows() -1 ) as $x ) {
					$this->fillCell( $x, $y, $values );
				}
				return true;
			}

			if ( $values === array() ) return false;

			$length = $this->countRows();
			if ( is_array( $values ) && $length == count( $values ) ) {
				for ( $x = 0; $x < $length; $x++ ) {
					$this->fillCell( $x, $y, $values[$x] );
				}
				return true;
			}

			return false;
		}

		/**
 		 * Column Remover
 		 * Completly removes a whole column identified by $name
 		 * Note: that this function will only work if data is symmetric.
 		 *
 		 * @param string $name same as the ones returned by getHeaders();
 		 * @access public
 		 * @return boolean
 		 * @see hasColumn(), getHeaders(), createHeaders(), setHeaders(),
 		 * isSymmetric(), getAsymmetricRows()
 		 */
		public function removeColumn( $name ) {
			if ( !in_array( $name, $this->headers ) ) return false;

			if ( !$this->isSymmetric() ) return false;

			$key = array_search( $name, $this->headers );
			unset( $this->headers[$key] );
			$this->resetKeys( $this->headers );

			foreach ( $this->rows as $target => $row ) {
				unset( $this->rows[$target][$key] );
				$this->resetKeys( $this->rows[$target] );
			}

			return $this->isSymmetric();
		}

		/**
 		 * Column Walker
 		 * Goes through the whole column and executes a callback for each
 		 * one of the cells in it.
 		 *
 		 * Note: callback functions get the value of the cell as an
 		 * argument, and whatever that callback returns will be used to
 		 * replace the current value of that cell.
 		 *
 		 * @param string $name the header name used to identify the column
 		 * @param string $callback the callback function to be called per
 		 * each cell value
 		 *
 		 * @access public
 		 * @return boolean
 		 * @see getHeaders(), fillColumn(), appendColumn()
 		 */
		public function walkColumn( $name, $callback ) {
			if ( !$this->isSymmetric() ) return false;

			if ( !$this->hasColumn( $name ) ) return false;

			if ( !function_exists( $callback ) ) return false;

			$column = $this->getColumn( $name );
			foreach ( $column as $key => $cell ) {
				$column[$key] = $callback( $cell );
			}
			return $this->fillColumn( $name, $column );
		}

		/**
 		 * Cell Fetcher
 		 * Gets the value of a specific cell by given coordinates
 		 *
 		 * Note: That indexes start with zero, and headers are not
 		 * searched.
 		 *
 		 * @access public
 		 * @return mixed|false the value of the cell or false if the cell does not exist
 		 * @see getHeaders(), hasCell(), getRow(), getRows(), getColumn()
 		 */
		public function getCell( $x, $y ) {
			if ( $this->hasCell( $x, $y ) ) {
				$row = $this->getRow( $x );
				return $row[$y];
			}
			return false;
		}

		/**
 		 * Cell Value Filler
 		 * Replaces the value of a specific cell
 		 *
 		 * @param integer $x the row to fetch
 		 * @param integer $y the column to fetch
 		 * @param mixed $value the value to fill the cell with
 		 * @access public
 		 * @return boolean
 		 * @see hasCell(), getRow(), getRows(), getColumn()
 		 */
		public function fillCell( $x, $y, $value ) {
			if ( !$this->hasCell( $x, $y ) ) return false;

			$row = $this->getRow( $x );
			$row[$y] = $value;
			$this->rows[$x] = $row;

			return true;
		}

		/**
 		 * Checks if a coordinate is valid
 		 *
 		 * @param mixed $x the row to fetch
 		 * @param mixed $y the column to fetch
 		 * @access public
 		 * @return void
 		 */
		public function hasCell( $x, $y ) {
			$has_x = array_key_exists( $x, $this->rows );
			$has_y = array_key_exists( $y, $this->headers );
			return ( $has_x && $has_y );
		}

		/**
 		 * Row Fetcher
 		 * Note: first row is zero
 		 *
 		 * @param integer $number the row number to fetch
 		 * @access public
 		 * @return array the row identified by number, if $number does
 		 * not exist an empty array is returned instead
 		 */
		public function getRow( $number ) {
			$raw = $this->rows;
			if ( array_key_exists( $number, $raw ) ) {
				return $raw[$number];
			}
			return array();
		}

		/**
 		 * Multiple Row Fetcher
 		 * Extracts a rows in the following fashion
 		 *   - all rows if no $range argument is given
 		 *   - a range of rows identified by their key
 		 *   - if rows in range are not found nothing is retrived instead
 		 *   - if no rows were found an empty array is returned
 		 *
 		 * @param array $range a list of rows to retrive
 		 * @access public
 		 * @return array
 		 */
		public function getRows( $range = array() ) {
			if ( is_array( $range ) && ( $range === array() ) ) return $this->rows;

			if ( !is_array( $range ) ) return $this->rows;

			$ret_arr = array();
			foreach ( $this->rows as $key => $row ) {
				if ( in_array( $key, $range ) ) {
					$ret_arr[] = $row;
				}
			}
			return $ret_arr;
		}

		/**
 		 * Row Counter
 		 * This function will exclude the headers
 		 *
 		 * @access public
 		 * @return integer
 		 */
		public function countRows() {
			return count( $this->rows );
		}

		/**
 		 * Row Appender
 		 * Aggregates one more row to the currently loaded dataset
 		 *
 		 * @param array $values the values to be appended to the row
 		 * @access public
 		 * @return boolean
 		 */
		public function appendRow( $values ) {
			$this->rows[] = array();
			$this->symmetrize();
			return $this->fillRow( $this->countRows() - 1, $values );
		}

		/**
 		 * Fill Row
 		 * Replaces the contents of cells in one given row with $values.
 		 *
 		 * @param integer $row		the row to fill identified by its key
 		 * @param mixed   $values the value to use, if a string or number
 		 * is given the whole row will be replaced with this value.
 		 * if an array is given instead the values will be used to fill
 		 * the row. Only when the currently loaded dataset is symmetric
 		 * @access public
 		 * @return boolean
 		 * @see isSymmetric(), getAsymmetricRows(), symmetrize(), fillColumn(),
 		 * fillCell(), appendRow()
 		 */
		public function fillRow( $row, $values ) {
			if ( !$this->hasRow( $row ) ) return false;

			if ( is_string( $values ) || is_numeric( $values ) ) {
				foreach ( $this->rows[$row] as $key => $cell ) {
 					$this->rows[$row][$key] = $values;
				}
				return true;
			}

			$eql_to_headers = ( $this->countHeaders() == count( $values ) );
			if ( is_array( $values ) && $this->isSymmetric() && $eql_to_headers ) {
				$this->rows[$row] = $values;
				return true;
			}

			return false;
		}

		/**
 		 * Row Existance Checker
 		 * Scans currently loaded dataset and checks if a given row identified by $number exists
 		 *
 		 * @param mixed $number a numeric value that identifies the row you are trying to fetch.
 		 * @access public
 		 * @return boolean
 		 * @see getRow(), getRows(), appendRow(), fillRow()
 		 */
		public function hasRow( $number ) {
			return ( in_array( $number, array_keys( $this->rows ) ) );
		}

		/**
 		 * Row Remover
 		 * Removes one row from the current data set.
 		 *
 		 * @param mixed $number the key that identifies that row
 		 * @access public
 		 * @return boolean
 		 * @see hasColumn(), getHeaders(), createHeaders(), setHeaders(),
 		 * isSymmetric(), getAsymmetricRows()
 		 */
		public function removeRow( $number ) {
			$cnt = $this->countRows();
			$row = $this->getRow( $number );
			if ( is_array( $row ) && ( $row != array() ) ) {
				unset( $this->rows[$number] );
			} else {
				return false;
			}
			$this->resetKeys( $this->rows );
			return ( $cnt == ( $this->countRows() + 1 ) );
		}

		/**
 		 * Row Walker
 		 * Goes through one full row of data and executes a callback function per each cell in that row.
 		 *
 		 * Note: callback functions get the value of the cell as an argument, and whatever that callback
 		 * returns will be used to replace the current value of that cell.
 		 *
 		 * @param string|integer $row anything that is numeric is a valid row identificator. As long as 
 		 * it is within the range of the currently loaded dataset
 		 * @param string $callback the callback function to be executed per each cell in a row
 		 * @access public
 		 * @return boolean
 		 *  - false if callback does not exist
 		 *  - false if row does not exits
 		 */
		public function walkRow( $row, $callback ) {
			if ( !function_exists( $callback ) ) return false;

			if ( $this->hasRow( $row ) ) {
				foreach ( $this->getRow( $row ) as $key => $value ) {
					$this->rows[$row][$key] = $callback( $value );
				}
				return true;
			}
			return false;
		}

		/**
 		 * Raw Data as array
 		 * Gets the data that was retrived from the csv file as an array
 		 *
 		 * Note: that changes and alterations made to rows, columns and values will
 		 * also reflect on what this function retrives.
 		 *
 		 * @access public
 		 * @return array
 		 * @see connect(), getHeaders(), getRows(), isSymmetric(), getAsymmetricRows(),
 		 * symmetrize()
 		 */
		public function getRawArray() {
			$ret_arr = array();
			$ret_arr[] = $this->headers;
			foreach ( $this->rows as $row ) {
				$ret_arr[] = $row;
			}
			return $ret_arr;
		}

		/**
 		 * Header Creator
 		 * Uses prefix and creates a header for each column suffixed by a numeric value
 		 * By default the first row is interpreted as headers but if we have a csv file with
 		 * data only and no headers it becomes really annoying to work with the current loaded data.
 		 * This function will create a set dinamically generated headers and make the current
 		 * headers accessable with the row handling functions
 		 *
 		 * Note: that the csv file contains only data but no headers
 		 *
 		 * @param string $prefix string to use as prefix for each independent header
 		 * @access public
 		 * @return boolean fails if data is not symmetric
 		 * @see isSymmetric(), getAsymmetricRows()
 		 */
		public function createHeaders( $prefix ) {
			if ( !$this->isSymmetric() ) return false;

			$length = count( $this->headers ) + 1;
			$this->moveHeadersToRows();

			$ret_arr = array();
			for ( $i = 1; $i < $length; $i++ ) {
				$ret_arr[] = $prefix . "_$i";
			}
			$this->headers = $ret_arr;
			return $this->isSymmetric();
		}

		/**
 		 * Header Injector
 		 * Uses a $list of values which wil be used to replace current headers.
 		 *
 		 * Note: that given $list must match the length of all rows.
 		 * Known as symmetric. see isSymmetric() and getAsymmetricRows() methods
 		 * Also, that current headers will be used as first row of data
 		 * and consecuently all rows order will change with this action.
 		 *
 		 * @param array $list a collection of names to use as headers,
 		 * @access public
 		 * @return boolean fails if data is not symmetric
 		 * @see isSymmetric(), getAsymmetricRows(), getHeaders(), createHeaders()
 		 */
		public function setHeaders( $list ) {
			if ( !$this->isSymmetric() ) return false;

			if ( !is_array( $list ) ) return false;

			if ( count( $list ) != count( $this->headers ) ) return false;

			$this->moveHeadersToRows();
			$this->headers = $list;
			return true;
		}

		/**
 		 * CSV Parser
 		 * Reads csv data and transforms it into php-data
 		 *
 		 * @access protected
 		 * @return boolean
 		 */
		protected function parse() {
			if ( !$this->validates() ) return false;

			$c = 0;
			$d = $this->settings['delimiter'];
			$e = $this->settings['escape'];
			$l = $this->settings['length'];

			$res = fopen( $this->_filename, 'r' );

			while ( $keys = fgetcsv( $res, $l, $d, $e ) ) {

				if ( $c == 0 ) {
					$this->headers = $keys;
				} else {
					array_push( $this->rows, $keys );
				}
				$c ++;
			}

			fclose( $res );
			$this->removeEmpty();
			return true;
		}

		/**
 		 * Empty row remover
 		 * Removes all records that have been defined but have no data.
 		 *
 		 * @access protected
 		 * @return array containing only the rows that have data
 		 */
		protected function removeEmpty() {
			$ret_arr = array();
			foreach ( $this->rows as $row ) {
				$line = trim( join( '', $row ) );
				if ( !empty( $line ) ) {
					$ret_arr[] = $row;
				}
			}
			$this->rows = $ret_arr;
		}

		/**
 		 * CSV File Validator
 		 * Checks wheather if the given csv file is valid or not
 		 *
 		 * @access protected
 		 * @return boolean
 		 */
		protected function validates() {
			// file existance
			if ( !file_exists( $this->_filename ) ) return false;

			// file readability
			if ( !is_readable( $this->_filename ) ) return false;

			return true;
		}

		/**
 		 * Header Relocator
 		 * @access protected
 		 * @return void
 		 */
		protected function moveHeadersToRows() {
			$arr  = array();
			$arr[] = $this->headers;
			foreach ( $this->rows as $row ) {
				$arr[] = $row;
			}
			$this->rows = $arr;
			$this->headers = array();
		}

		/**
 		 * Array Key Reseter
 		 * Makes sure that an array's keys are setted in a correct numerical order
 		 *
 		 * Note: that this function does not return anything, all changes are made to
 		 * the original array as a reference
 		 *
 		 * @param array &$array any array, if keys are strings they will be replaced with numeric values
 		 * @access protected
 		 * @return void
 		 */
		protected function resetKeys( &$array ) {
			$arr = array();
			foreach ( $array as $item ) {
				$arr[] = $item;
			}
			$array = $arr;
		}

		/**
 		 * Object Data Flusher
 		 * Tells this object to forget all data loaded and start from scratch
 		 * @access protected
 		 * @return void
 		 */
		protected function flush() {
			$this->rows	= array();
			$this->headers = array();
		}
	}
}
?>