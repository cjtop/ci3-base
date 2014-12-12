<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014, British Columbia Institute of Technology
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
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @author  Chuck Topinka
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @link    http://chucktopinka.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller all other controllers should extend
 * 
 * Extends CI_Controller and provides additional application setup.
 * @author Chuck Topinka
 *
 */
class MY_Controller extends CI_Controller
{
    
    /**
     * Holds the data for the view
     * @var array
     */
    public $data = array();
    
    /**
     * Whether to profile the application
     * @var boolean
     */
    protected $profile_this = false;
    
    public function __construct()
    {
        parent::__construct();
        
        // Styling and profiling
        if (ENVIRONMENT == 'development') {
            // Profiler is always on in development
            $this->profile_this = TRUE;
        }
        $this->output->enable_profiler($this->profile_this);
                
        // Ensure database is current
        /*
        $this->load->library('migration');
        
        if ( ! $this->migration->current()) {
            show_error($this->migration->error_string());
            log_message('error', 'Migration error: '.$this->migration->error_string());
        }
        */
        
        // Ensure css is current
        $this->autoCompileLess();
        
        log_message('debug', 'MY_Controller class initialized');
    }
    
    /**
     * Compile less to css with caching
     * 
     * Uses less library at application/libraries/lessc/Lessc.php. Uses http://leafo.net/lessphp/
     * @link http://leafo.net/lessphp/
     */
    protected function autoCompileLess()
    {
        $this->load->helper('url');
        $home = $this->config->item('public_home');
        $inputFile = $home.'assets/less/template.less';
        $outputFile = $home.'assets/css/template.css';
        
        // load the cache
        $cacheFile = $inputFile.".cache";
        
        if (file_exists($cacheFile)) {
            $cache = unserialize(file_get_contents($cacheFile));
        } else {
            $cache = $inputFile;
        }

        $this->load->library('lessc/lessc');
        $newCache = $this->lessc->cachedCompile($cache);
        
        if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
            log_message('debug', 'Compiling LESS to CSS.');
            file_put_contents($cacheFile, serialize($newCache));
            file_put_contents($outputFile, $newCache['compiled']);
        } else {
            log_message('debug', 'CSS up to date.');
        }
    }
    
    /**
     * Renders an HTML view using the template
     * @param string | array $view The view(s) to render.
     */
    protected function renderHtml($view = NULL)
    {
        // Default view to current class
        if (is_null($view)) { 
            $view = strtolower(get_class($this));
        }

        // Load helpers
        $this->load->helper('url');
        
        // Get the view(s) and place in view_output
        if (is_array($view)) {
            foreach ($view as $subview) {
                log_message('debug', 'Rendering '.$subview);
                $this->data['view_output'] .= $this->load->view($view, $this->data, TRUE);
            }
        } else {
            log_message('debug', 'Rendering '.$view);
            $this->data['view_output'] = $this->load->view($view, $this->data, TRUE);
        }
        
        // Load the site template
        log_message('debug', 'Using site template.');
        	
        // Set title if not set already
        if (!array_key_exists('meta_title', $this->data)) {
            $this->data['meta_title'] = $view;
        }
        $this->load->view('templates/site', $this->data);
    }
}