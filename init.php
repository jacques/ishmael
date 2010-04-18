<?php
	require_once('conf.php');

	function _format_query_callback($str) {
		$fragment_class = "keyword";
		if (preg_match("/^\s*\/\*.*?\*\/\s*$/", $str[0])) {
			$fragment_class = "comment";
		}
		return "<span class=\"{$fragment_class}\">" . $str[0] . "</span>";
	}

	function format_query($query) {
		$pattern = "/(?i)(\/\*.*?\*\/)|(\b(SELECT|INSERT|UPDATE|DELETE|FROM|WHERE|(INNER|OUTER|STRAIGHT|RIGHT|LEFT(?:\s?))?JOIN|ORDER BY|GROUP BY|DESC|FORCE INDEX|USE INDEX|BETWEEN|USING|AND|IN|OR|AS|ON)\b)/";
		$query = preg_replace_callback($pattern, '_format_query_callback', $query);

		$query = preg_replace("/(\w)\s*,/", "$1, ", $query);

		return "<span class=\"query\">{$query}</span>";
	}

	# get the list of hosts this ishmael install is configured to look at
	function ish_get_host_list() {
		global $conf;
		return array_keys($conf['hosts']);
	}

	# merges the config for a particular host on top of the defaults
	function ish_get_host_config($host) {
		global $conf;
		$defaults = $conf;
		unset($defaults['hosts']);
		$host_config = array_merge($defaults, $conf['hosts'][$host]);
		$host_config['db_host'] = $host;
		return $host_config;
	}

	# build up a query string using 1. things we need in every URL and 
	# 2. whatever is passed in $args as k-v pairs
	function ish_build_query($args) {
		global $host;
		$always_need = array(
			'host' => $host,
		);
		$final_args = array_merge($always_need, $args);
		return http_build_query($final_args);
	}

	$hosts = ish_get_host_list();

	$host = $_GET['host'] ? $_GET['host'] : $hosts[0];

	$host_conf = ish_get_host_config($host);

	mysql_connect($host_conf['db_host'],$host_conf['db_user'],$host_conf['db_password']);
	@mysql_select_db($host_conf['db_database_mk']) or die("Unable to select database");
