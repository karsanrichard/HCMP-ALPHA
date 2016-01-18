<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
Copyright (C) 2011 by Jim Saunders
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
class Github_updater
{
    const API_URL = 'https://api.github.com/repos/';
    const GITHUB_URL = 'https://github.com/';
    const CONFIG_FILE = 'application/config/github_updater.php';
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->config('github_updater');
    }
    /**
     * Checks if the current version is up to date
     *
     * @return bool true if there is an update and false otherwise
     */
    public function has_update()
    {
        $branches = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches'));
        // $branches_raw = $this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches');
        // $var = API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches';
        // return $branches_raw;
        // return $branches;
        // return $branches[0]->commit->sha;
        return $branches[0]->commit->sha !== $this->ci->config->item('current_commit');
        
    }
    public function get_hash()
    {
        $branches = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches'));
        // $branches_raw = $this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches');
        // $var = API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches';
        // return $branches_raw;
        // return $branches;
        return $branches[0]->commit->sha;
        // return $branches[0]->commit->sha !== $this->ci->config->item('current_commit');
        
    }
    /**
     * If there is an update available get an array of all of the
     * commit messages between the versions
     *
     * @return array of the messages or false if no update
     */
    public function get_update_comments()
    {
        $branches = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches'));
        $hash = $branches[0]->commit->sha;
        if($hash !== $this->ci->config->item('current_commit'))
        {
            $messages = array();
            $response = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/compare/'.$this->ci->config->item('current_commit').'...'.$hash));
            $commits = $response->commits;
            foreach($commits as $commit)
                $messages[] = $commit->commit->message;
            return $messages;
        }
        return false;
        // $response_stmt = API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/compare/'.$this->ci->config->item('current_commit').'...'.$hash;
        // return $response_stmt;
    }
    /**
     * Performs an update if one is available.
     *
     * @return bool true on success, false on failure
     */
    public function update()
    {
        $branches = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches'));
        $hash = $branches[0]->commit->sha;
        if($hash !== $this->ci->config->item('current_commit'))
        {
            $commits = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/compare/'.$this->ci->config->item('current_commit').'...'.$hash));
            $files = $commits->files;
            // return $files;
            // $dir = $this->_get_and_extract($hash);
           /* foreach ($files as $file) {
                if(!$this->_is_ignored($file->filename))
                    {
                        echo "<pre>" .$file->filename." Im ignored ". $file->status ."</pre>";
                    }
                    else{
                        echo "<pre>" .$file->filename." Im not ignored ". $file->status ."</pre>";

                        if($file->status == 'removed')unlink($file->filename);
                        //Otherwise copy the file from the update.
                        else copy($file->filename, $file->filename);
                    }
            }*/

            // echo $this->_get_and_extract($hash);

            if($dir = $this->_get_and_extract($hash))
            {
                //Loop through the list of changed files for this commit
                foreach($files as $file)
                {
                    //If the file isn't in the ignored list then perform the update
                    if(!$this->_is_ignored($file->filename))
                    {
                        //If the status is removed then delete the file
                        // return $file->status;
                        if($file->status === 'removed')unlink($file->filename);
                        //Otherwise copy the file from the update.
                        else copy($dir.'/'.$file->filename, $file->filename);
                    }
                }
                //Clean up
                if($this->ci->config->item('clean_update_files'))
                {
                    shell_exec("rm -rf {$dir}");
                    unlink("{$hash}.zip");
                }
                //Update the current commit hash
                $this->_set_config_hash($hash);
                return "Cleared Files";
            }
        }
        return false;
    }

    public function update_download()
    {
        $branches = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/branches'));
        $hash = $branches[0]->commit->sha;
        if($hash !== $this->ci->config->item('current_commit'))
        {
            $commits = json_decode($this->_connect(self::API_URL.$this->ci->config->item('github_user').'/'.$this->ci->config->item('github_repo').'/compare/'.$this->ci->config->item('current_commit').'...'.$hash));
            $files = $commits->files;
            // return $files;
            $dir = $this->_get_and_extract($hash);
           /* foreach ($files as $file) {
                if(!$this->_is_ignored($file->filename))
                    {
                        echo "<pre>" .$file->filename." Im ignored ". $file->status ."</pre>";
                    }
                    else{
                        echo "<pre>" .$file->filename." Im not ignored ". $file->status ."</pre>";

                        if($file->status == 'removed')unlink($file->filename);
                        //Otherwise copy the file from the update.
                        else copy($file->filename, $file->filename);
                    }
            }*/

            // echo $this->_get_and_extract($hash);

            /*if($dir = $this->_get_and_extract($hash))
            {
                //Clean up
                if($this->ci->config->item('clean_update_files'))
                {
                    shell_exec("rm -rf {$dir}");
                    unlink("{$hash}.zip");
                }
                //Update the current commit hash
                $this->_set_config_hash($hash);
            }*/
        }
        return true;
    }

    public function _is_ignored($filename)
    {
        $ignored = $this->ci->config->item('ignored_files');
        foreach($ignored as $ignore)
            if(strpos($filename, $ignore) !== false)return true;
        return false;
    }

    public function list_ignored()
    {
        $ignored = $this->ci->config->item('ignored_files');
        return $ignored;
    }

    public function _set_config_hash($hash)
    {
        $lines = file(self::CONFIG_FILE, FILE_IGNORE_NEW_LINES);
        $count = count($lines);
        for($i=0; $i < $count; $i++)
        {
            $configline = '$config[\'current_commit\']';
            if(strstr($lines[$i], $configline))
            {
                $lines[$i] = $configline.' = \''.$hash.'\';';
                $file = implode(PHP_EOL, $lines);
                $handle = @fopen(self::CONFIG_FILE, 'w');
                fwrite($handle, $file);
                fclose($handle);
                return true;
            }
        }
        return false;
    }
    private function _get_and_extract($hash)
    {
       copy(self::GITHUB_URL . $this -> ci -> config -> item('github_user') . '/' . $this -> ci -> config -> item('github_repo') . '/zipball/' . $this -> ci -> config -> item('github_branch'), "{$hash}.zip");
        shell_exec("unzip {$hash}.zip");
        $files = scandir('.');
        foreach ($files as $file)
            if (strpos($file, $this -> ci -> config -> item('github_user') . '-' . $this -> ci -> config -> item('github_repo')) !== FALSE)
                return $file;

        return false;
    }

    public function get_commit_zip($hash){
       $git_files = copy(self::GITHUB_URL . $this -> ci -> config -> item('github_user') . '/' . $this -> ci -> config -> item('github_repo') . '/zipball/' . $this -> ci -> config -> item('github_branch'), "{$hash}.zip");

       // $var = GITHUB_URL . $this -> ci -> config -> item('github_user') . '/' . $this -> ci -> config -> item('github_repo') . '/zipball/' . $this -> ci -> config -> item('github_branch');

       // echo $var;
       return $git_files;
    }

    private function _connect($url) {
        $curl = new Curl();
        $curl -> setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl -> setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl -> get($url);

        if ($curl -> error) {
            $curl -> error_code;
            $response = "Error: " . $curl -> error_code;
        } else {
            $response = $curl -> response;
        }
        return $response;
    }
}