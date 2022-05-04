<?php
/*
Plugin Name: A2 Fixed W3 Total Cache
Description: A community driven build of W3 Total Cache originally developed by @ftownes. The aim is to continuously incorporate fixes, improvements, and enhancements over the official Wordpress release of W3 Total Cache.
Version: 0.9.4.6.4.8.1
Network: True
*/

/*  Copyright (c) 2009 Frederick Townes <ftownes@w3-edge.com>
	Portions of this distribution are copyrighted by:
		Copyright (c) 2008 Ryan Grove <ryan@wonko.com>
		Copyright (c) 2008 Steve Clay <steve@mrclay.org>
				Copyright (c) 2007 Matt Mullenweg
				Copyright (c) 2007 Andy Skelton
				Copyright (c) 2007 Iliya Polihronov
				Copyright (c) 2007 Michael Adams
				Copyright (c) 2007 Automattic Inc.
				Ryan Boren
	All rights reserved.

	W3 Total Cache is distributed under the GNU General Public License, Version 2,
	June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
	St, Fifth Floor, Boston, MA 02110, USA

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

if (!defined('ABSPATH')) {
	die();
}

/**
 * Abort W3TC loading if WordPress is upgrading
 */
if (defined('WP_INSTALLING') && WP_INSTALLING) {
	return;
}

include dirname(__FILE__) . '/lib/EDD/integration.php';

if (!defined('W3TC_IN_MINIFY')) {
	/**
	 * Require plugin configuration
	 */
	require_once dirname(__FILE__) . '/inc/define.php';

	// Load the wp cli command - if run from wp-cli
	if (defined('WP_CLI') && WP_CLI) {
		w3_require_once(W3TC_LIB_W3_DIR . '/Cli.php');
	}

	/**
	 * Run
	 */
	$root = w3_instance('W3_Root');
	$root->run();
}

/**
 * Remote Updater
 */
define( 'W3TC_UPDATE_URL', 'http://wp-plugins.a2hosting.com/?update' );

add_action( 'plugins_loaded', 'w3tc_load_updater' );
function w3tc_load_updater() {
	if ( ! class_exists( 'W3_Remote_Updater' ) ) {
		include dirname(__FILE__) . '/class.W3RemoteUpdater.php';
	}
}

add_action( 'admin_init', 'w3_remote_update' );
function w3_remote_update() {
	$a2_plugins = array(
		'a2-w3-total-cache/a2-w3-total-cache.php' => 'tpHpOJvLcQ5TLdh5',
	);

	// ensure the class exists before running
	if ( ! class_exists( 'W3_Remote_Updater' ) ) {
		include dirname(__FILE__) . '/class.W3RemoteUpdater.php';
	}

	$updaters = array();
	$plugins = get_plugins();

	foreach ($a2_plugins as $plugin => $unique) {
		$updaters[] = new W3_Remote_Updater(
			W3TC_UPDATE_URL,
			$plugin,
			array(
			'unique' => $unique,
			'version' => isset($plugins[$plugin]) ? $plugins[$plugin]['Version'] : '0.0.0'
			)
		);
	}
}

/********************************/
if (!defined('ABSPATH')) {
	die();
}

/**
 * Abort W3TC loading if WordPress is upgrading
 */
if (defined('WP_INSTALLING') && WP_INSTALLING) {
	return;
}

if ( !defined('W3TC_IN_MINIFY') ) {
	/**
	 * Require plugin configuration
	 */
	require_once dirname(__FILE__) . '/inc/define.php';

	// Load the wp cli command - if run from wp-cli
	if (defined('WP_CLI') && WP_CLI) {
		w3_require_once(W3TC_LIB_W3_DIR . '/Cli.php');
	}

	/**
	 * Run
	 */
	$root = w3_instance('W3_Root');
	$root->run();
}
