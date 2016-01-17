<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The user name of the git hub user who owns the repo
 */
// $config['github_user'] = 'HCMPKenya';
$config['github_user'] = 'karsanrichard';

/**
 * The repo on GitHub we will be updating from
 */
$config['github_repo'] = 'HCMP-ALPHA';

/**
 * The branch to update from
 */
$config['github_branch'] = 'master';

/**
 * The current commit the files are on.
 * 
 * NOTE: You should only need to set this initially it will be
 * automatically set by the library after subsequent updates.
 */
$config['current_commit'] = 'e1b8d131fb5f7a9b135dea3537b6f4cbaa0b44f3';

/**
 * A list of files or folders to never perform an update on.
 * Not specifying a relative path from the webroot will apply
 * the ignore to any files with a matching segment.
 *
 * I.E. Specifying 'admin' as an ignore will ignore
 * 'application/controllers/admin.php'
 * 'application/views/admin/test.php'
 * and any other path with the term 'admin' in it.
 */
$config['ignored_files'] = array('application/config','.gitignore','user_guide','.htaccess','.idea','print_docs','mpdf');

/**
 * Flag to indicate if the downloaded and extracted update files
 * should be removed
 */
$config['clean_update_files'] = false;