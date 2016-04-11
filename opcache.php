<?php

define('THOUSAND_SEPARATOR',true);

if (!extension_loaded('Zend OPcache')) {
    echo '<div style="background-color: #F2DEDE; color: #B94A48; padding: 1em;">该服务器未加载Opcache扩展,下面显示的为示例数据.</div>';
    require 'data-sample.php';
}

class OpCacheDataModel
{
    private $_configuration;
    private $_status;
    private $_d3Scripts = array();

    public function __construct()
    {
        $this->_configuration = opcache_get_configuration();
        $this->_status = opcache_get_status();
        if(isset($_GET['opcache_reset'])){
            if(opcache_reset()){
                echo '<div style="background-color: #F2DEDE; color: #B94A48; padding: 1em;">操作码缓存已重置 <span><a href="javascript:void(0)" onclick="$(this).parent().parent().hide();">&#10006;</a></span></div>';
            }else{
                echo '<div style="background-color: #F2DEDE; color: #B94A48; padding: 1em;">操作码缓存重置失败<span><a href="javascript:void(0)" onclick="$(this).parent().parent().hide();">&#10006;</a></span></div>';
            }
        }
    }

    public function getPageTitle()
    {
        return 'PHP ' . phpversion() . " / OpCache {$this->_configuration['version']['version']}";
    }

    public function translate($k){
        $t['used_memory'] = '已用内存';
        $t['free_memory'] = '可用内存';
        $t['wasted_memory'] = '浪费内存';
        $t['opcache_hit_rate'] = '命中率';
        $t['blacklist_miss_ratio'] = '白名单未命中率';
        $t['start_time'] = '启动时间';
        $t['last_restart_time'] = '上次启动时间';
        $t['num_cached_scripts'] = '缓存脚本';
        $t['num_cached_keys'] = '缓存键';
        $t['max_cached_keys'] = '最大缓存键';
        $t['hits'] = '命中';
        $t['misses'] = '未命中';
        $t['blacklist_misses'] = '白名单未命中';
        $t['oom_restarts'] = '因内存溢出重启';
        $t['manual_restarts'] = '手动重启';
        $t['hash_restarts'] = '因hash重启';
        $t['current_wasted_percentage'] = '当前浪费率';
        $t['opcache_enabled'] = '启用操作码缓存';

        $t['opcache.enable'] = '启用操作码缓存';
        $t['opcache.enable_cli'] = '仅针对 CLI 版本的 PHP 启用操作码缓存';
        $t['opcache.memory_consumption'] = 'OPcache 的共享内存大小，以兆字节为单位';
        $t['opcache.interned_strings_buffer'] = '用来存储临时字符串的内存大小，以兆字节为单位';
        $t['opcache.max_accelerated_files'] = 'OPcache 哈希表中可存储的脚本文件数量上限。设置值取值范围最小值是 200，最大值在 PHP 5.5.6 之前是 100000，PHP 5.5.6 及之后是 1000000 ';
        $t['opcache.max_wasted_percentage'] = '浪费内存的上限，以百分比计。 如果达到此上限，那么 OPcache 将产生重新启动续发事件';
        $t['opcache.use_cwd'] = '如果启用，OPcache 将在哈希表的脚本键之后附加改脚本的工作目录， 以避免同名脚本冲突的问题。 禁用此选项可以提高性能，但是可能会导致应用崩溃。';
        $t['opcache.validate_timestamps'] = '如果启用，那么 OPcache 会每隔 opcache.revalidate_freq 设定的秒数 检查脚本是否更新。 如果禁用此选项，你必须使用 opcache_reset() 或者 opcache_invalidate() 函数来手动重置 OPcache，也可以 通过重启 Web 服务器来使文件系统更改生效。';
        $t['opcache.revalidate_freq'] = '检查脚本时间戳是否有更新的周期，以秒为单位。 设置为 0 会导致针对每个请求， OPcache 都会检查脚本更新';
        $t['opcache.revalidate_path'] = '如果禁用此选项，在同一个 include_path 已存在的缓存文件会被重用。 因此，将无法找到不在包含路径下的同名文件。';
        $t['opcache.save_comments'] = '如果禁用，脚本文件中的注释内容将不会被包含到操作码缓存文件， 这样可以有效减小优化后的文件体积。 禁用此配置指令可能会导致一些依赖注释或注解的 应用或框架无法正常工作， 比如： Doctrine， Zend Framework 2 以及 PHPUnit。';
        $t['opcache.load_comments'] = '如果禁用，则即使文件中包含注释，也不会加载这些注释内容。 本选项可以和 opcache.save_comments 一起使用，以实现按需加载注释内容。';
        $t['opcache.fast_shutdown'] = '如果启用，则会使用快速停止续发事件。 所谓快速停止续发事件是指依赖 Zend 引擎的内存管理模块 一次释放全部请求变量的内存，而不是依次释放每一个已分配的内存块。';
        $t['opcache.enable_file_override'] = '如果启用，则在调用函数 file_exists()， is_file() 以及 is_readable() 的时候， 都会检查操作码缓存，无论文件是否已经被缓存。 如果应用中包含检查 PHP 脚本存在性和可读性的功能，这样可以提升性能。 但是如果禁用了 opcache.validate_timestamps 选项， 可能存在返回过时数据的风险。';
        $t['opcache.optimization_level'] = '控制优化级别的二进制位掩码。';
        $t['opcache.inherited_hack'] = '在 PHP 5.3 之前的版本，OPcache 会存储代码中使用 DECLARE_CLASS 操作码 来实现继承的位置。当文件被加载之后，OPcache 会尝试使用当前环境来绑定被继承的类。 由于当前脚本中可能并不需要 DECLARE_CLASS 操作码，如果这样的脚本需要对应的操作码被定义时， 可能无法运行。';
        $t['opcache.dups_fix'] = '仅作为针对 “不可重定义类”错误的一种解决方案。';
        $t['opcache.blacklist_filename'] = 'OPcache 黑名单文件位置。 黑名单文件为文本文件，包含了不进行预编译优化的文件名，每行一个文件名。 黑名单中的文件名可以使用通配符，也可以使用前缀。 此文件中以分号（;）开头的行将被视为注释。';
        $t['opcache.max_file_size'] = '以字节为单位的缓存的文件大小上限。设置为 0 表示缓存全部文件。';
        $t['opcache.consistency_checks'] = '如果是非 0 值，OPcache 将会每隔 N 次请求检查缓存校验和。 N 即为此配置指令的设置值。 由于此选项对于性能有较大影响，请尽在调试环境使用。';
        $t['opcache.force_restart_timeout'] = '如果缓存处于非激活状态，等待多少秒之后计划重启。 如果超出了设定时间，则 OPcache 模块将杀除持有缓存锁的进程， 并进行重启。';
        $t['opcache.log_verbosity_level'] = 'OPcache 模块的日志级别。 默认情况下，仅有致命级别（0）及错误级别（1）的日志会被记录。 其他可用的级别有：警告（2），信息（3）和调试（4）。';
        $t['opcache.preferred_memory_model'] = 'OPcache 首选的内存模块。 如果留空，OPcache 会选择适用的模块， 通常情况下，自动选择就可以满足需求。';
        $t['opcache.error_log'] = 'OPcache 模块的错误日志文件。 如果留空，则视为 stderr， 错误日志将被送往标准错误输出 （通常情况下是 Web 服务器的错误日志文件）。';
        $t['opcache.protect_memory'] = '保护共享内存，以避免执行脚本时发生非预期的写入。 仅用于内部调试。';
        $t['opcache.lockfile_path'] = '';
        $t['opcache.file_cache'] = '';
        $t['opcache.file_cache_only'] = '';
        $t['opcache.file_cache_consistency_checks'] = '';
        if(isset($t[$k])){
            return $k.' - '.$t[$k];
        }else{
            return $k;
        }

    }

    public function getStatusDataRows()
    {
        $rows = array();
        foreach ($this->_status as $key => $value) {
            if ($key === 'scripts') {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($v === false) {
                        $value = 'false';
                    }
                    if ($v === true) {
                        $value = '是';
                    }
                    if ($k === 'used_memory' || $k === 'free_memory' || $k === 'wasted_memory') {
                        $v = $this->_size_for_humans(
                            $v
                        );
                    }
                    if ($k === 'current_wasted_percentage' || $k === 'opcache_hit_rate') {
                        $v = number_format(
                                $v,
                                2
                            ) . '%';
                    }
                    if ($k === 'blacklist_miss_ratio') {
                        $v = number_format($v, 2) . '%';
                    }
                    if ($k === 'start_time' || $k === 'last_restart_time') {
                        $v = ($v ? date(DATE_RFC822, $v) : 'never');
                    }
                    if (THOUSAND_SEPARATOR === true && is_int($v)) {
                        $v = number_format($v);
                    }

                    $rows[] = "<tr><th>".$this->translate($k)."</th><td>$v</td></tr>\n";
                }
                continue;
            }
            if ($value === false) {
                $value = '否';
            }
            if ($value === true) {
                $value = '是';
            }
            $rows[] = "<tr><th>".$this->translate($key)."</th><td>$value</td></tr>\n";
        }

        return implode("\n", $rows);
    }

    public function getConfigDataRows()
    {
        $rows = array();
        foreach ($this->_configuration['directives'] as $key => $value) {
            if ($value === false) {
                $value = '否';
            }
            if ($value === true) {
                $value = '是';
            }
            if ($key == 'opcache.memory_consumption') {
                $value = $this->_size_for_humans($value);
            }
            $rows[] = "<tr><th>".$this->translate($key)."</th><td>$value</td></tr>\n";
        }

        return implode("\n", $rows);
    }

    public function getScriptStatusRows()
    {
        foreach ($this->_status['scripts'] as $key => $data) {
            $dirs[dirname($key)][basename($key)] = $data;
            $this->_arrayPset($this->_d3Scripts, $key, array(
                'name' => basename($key),
                'size' => $data['memory_consumption'],
            ));
        }

        asort($dirs);

        $basename = '';
        while (true) {
            if (count($this->_d3Scripts) !=1) break;
            $basename .= DIRECTORY_SEPARATOR . key($this->_d3Scripts);
            $this->_d3Scripts = reset($this->_d3Scripts);
        }

        $this->_d3Scripts = $this->_processPartition($this->_d3Scripts, $basename);
        $id = 1;

        $rows = array();
        foreach ($dirs as $dir => $files) {
            $count = count($files);
            $file_plural = $count > 1 ? 's' : null;
            $m = 0;
            foreach ($files as $file => $data) {
                $m += $data["memory_consumption"];
            }
            $m = $this->_size_for_humans($m);

            if ($count > 1) {
                $rows[] = '<tr>';
                $rows[] = "<th class=\"clickable\" id=\"head-{$id}\" colspan=\"3\" onclick=\"toggleVisible('#head-{$id}', '#row-{$id}')\">{$dir} ({$count} file{$file_plural}, {$m})</th>";
                $rows[] = '</tr>';
            }

            foreach ($files as $file => $data) {
                $rows[] = "<tr id=\"row-{$id}\">";
                $rows[] = "<td>" . $this->_format_value($data["hits"]) . "</td>";
                $rows[] = "<td>" . $this->_size_for_humans($data["memory_consumption"]) . "</td>";
                $rows[] = $count > 1 ? "<td>{$file}</td>" : "<td>{$dir}/{$file}</td>";
                $rows[] = '</tr>';
            }

            ++$id;
        }

        return implode("\n", $rows);
    }

    public function getScriptStatusCount()
    {
        return count($this->_status["scripts"]);
    }

    public function getGraphDataSetJson()
    {
        $dataset = array();
        $dataset['memory'] = array(
            $this->_status['memory_usage']['used_memory'],
            $this->_status['memory_usage']['free_memory'],
            $this->_status['memory_usage']['wasted_memory'],
        );

        $dataset['keys'] = array(
            $this->_status['opcache_statistics']['num_cached_keys'],
            $this->_status['opcache_statistics']['max_cached_keys'] - $this->_status['opcache_statistics']['num_cached_keys'],
            0
        );

        $dataset['hits'] = array(
            $this->_status['opcache_statistics']['misses'],
            $this->_status['opcache_statistics']['hits'],
            0,
        );

        $dataset['restarts'] = array(
            $this->_status['opcache_statistics']['oom_restarts'],
            $this->_status['opcache_statistics']['manual_restarts'],
            $this->_status['opcache_statistics']['hash_restarts'],
        );

        if (THOUSAND_SEPARATOR === true) {
            $dataset['TSEP'] = 1;
        } else {
            $dataset['TSEP'] = 0;
        }

        return json_encode($dataset);
    }

    public function getHumanUsedMemory()
    {
        return $this->_size_for_humans($this->getUsedMemory());
    }

    public function getHumanFreeMemory()
    {
        return $this->_size_for_humans($this->getFreeMemory());
    }

    public function getHumanWastedMemory()
    {
        return $this->_size_for_humans($this->getWastedMemory());
    }

    public function getUsedMemory()
    {
        return $this->_status['memory_usage']['used_memory'];
    }

    public function getFreeMemory()
    {
        return $this->_status['memory_usage']['free_memory'];
    }

    public function getWastedMemory()
    {
        return $this->_status['memory_usage']['wasted_memory'];
    }

    public function getWastedMemoryPercentage()
    {
        return number_format($this->_status['memory_usage']['current_wasted_percentage'], 2);
    }

    public function getD3Scripts()
    {
        return $this->_d3Scripts;
    }

    private function _processPartition($value, $name = null)
    {
        if (array_key_exists('size', $value)) {
            return $value;
        }

        $array = array('name' => $name,'children' => array());

        foreach ($value as $k => $v) {
            $array['children'][] = $this->_processPartition($v, $k);
        }

        return $array;
    }

    private function _format_value($value)
    {
        if (THOUSAND_SEPARATOR === true) {
            return number_format($value);
        } else {
            return $value;
        }
    }

    private function _size_for_humans($bytes)
    {
        if ($bytes > 1048576) {
            return sprintf('%.2f&nbsp;MB', $bytes / 1048576);
        } else {
            if ($bytes > 1024) {
                return sprintf('%.2f&nbsp;kB', $bytes / 1024);
            } else {
                return sprintf('%d&nbsp;bytes', $bytes);
            }
        }
    }

    // Borrowed from Laravel
    private function _arrayPset(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;
        $keys = explode(DIRECTORY_SEPARATOR, ltrim($key, DIRECTORY_SEPARATOR));
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if ( ! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = array();
            }
            $array =& $array[$key];
        }
        $array[array_shift($keys)] = $value;
        return $array;
    }

}

$dataModel = new OpCacheDataModel();
?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <style>
        body {
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            margin: 0;
            padding: 0;
        }

        #container {
            width: 1024px;
            margin: auto;
            position: relative;
        }

        h1 {
            padding: 10px 0;
        }

        table {
            border-collapse: collapse;
        }

        tbody tr:nth-child(even) {
            background-color: #eee;
        }

        p.capitalize {
            text-transform: capitalize;
        }

        .tabs {
            position: relative;
            float: left;
            width: 60%;
        }

        .tab {
            float: left;
        }

        .tab label {
            background: #eee;
            padding: 10px 12px;
            border: 1px solid #ccc;
            margin-left: -1px;
            position: relative;
            left: 1px;
        }

        .tab [type=radio] {
            display: none;
        }

        .tab th, .tab td {
            padding: 8px 12px;
        }

        .content {
            position: absolute;
            top: 28px;
            left: 0;
            background: white;
            border: 1px solid #ccc;
            height: 640px;
            width: 100%;
            overflow: auto;
        }

        .content table {
            width: 100%;
        }

        .content th, .tab:nth-child(3) td {
            text-align: left;
        }

        .content td {
            text-align: right;
        }

        .clickable {
            cursor: pointer;
        }

        [type=radio]:checked ~ label {
            background: white;
            border-bottom: 1px solid white;
            z-index: 2;
        }

        [type=radio]:checked ~ label ~ .content {
            z-index: 1;
        }

        #graph {
            float: right;
            width: 40%;
            position: relative;
        }

        #graph > form {
            position: absolute;
            right: 60px;
            top: -20px;
        }

        #graph > svg {
            position: absolute;
            top: 0;
            right: 0;
        }

        #stats {
            position: absolute;
            right: 125px;
            top: 145px;
        }

        #stats th, #stats td {
            padding: 6px 10px;
            font-size: 0.8em;
        }

        #partition {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 10;
            top: 0;
            left: 0;
            background: #ddd;
            display: none;
        }

        #close-partition {
            display: none;
            position: absolute;
            z-index: 20;
            right: 15px;
            top: 15px;
            background: #f9373d;
            color: #fff;
            padding: 12px 15px;
        }

        #close-partition:hover {
            background: #D32F33;
            cursor: pointer;
        }

        #partition rect {
            stroke: #fff;
            fill: #aaa;
            fill-opacity: 1;
        }

        #partition rect.parent {
            cursor: pointer;
            fill: steelblue;
        }

        #partition text {
            pointer-events: none;
        }

        label {
            cursor: pointer;
        }
    </style>
    <script src="//cdn.bootcss.com/d3/3.5.16/d3.min.js"></script>
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script>
        var hidden = {};
        function toggleVisible(head, row) {
            if (!hidden[row]) {
                d3.selectAll(row).transition().style('display', 'none');
                hidden[row] = true;
                d3.select(head).transition().style('color', '#ccc');
            } else {
                d3.selectAll(row).transition().style('display');
                hidden[row] = false;
                d3.select(head).transition().style('color', '#000');
            }
        }
    </script>
    <title><?php echo $dataModel->getPageTitle(); ?></title>
</head>

<body>
    <div id="container">
        <h1><?php echo $dataModel->getPageTitle(); ?></h1>

        <div class="tabs">

            <div class="tab">
                <input type="radio" id="tab-status" name="tab-group-1" checked>
                <label for="tab-status">状态</label>
                <div class="content">
                    <table>
                        <?php echo $dataModel->getStatusDataRows(); ?>
                    </table>
                </div>
            </div>

            <div class="tab">
                <input type="radio" id="tab-config" name="tab-group-1">
                <label for="tab-config">配置信息</label>
                <div class="content">
                    <table>
                        <?php echo $dataModel->getConfigDataRows(); ?>
                    </table>
                    <div>
<pre>
    推荐配置:
    opcache.memory_consumption=128
    opcache.interned_strings_buffer=8
    opcache.max_accelerated_files=4000
    opcache.revalidate_freq=60
    opcache.fast_shutdown=1
    opcache.enable_cli=1
</pre></div>
                </div>
            </div>

            <div class="tab">
                <input type="radio" id="tab-scripts" name="tab-group-1">
                <label for="tab-scripts">脚本 (<?php echo $dataModel->getScriptStatusCount(); ?>)</label>
                <div class="content">
                    <table style="font-size:0.8em;">
                        <tr>
                            <th width="10%"></th>
                            <th width="20%">内存</th>
                            <th width="70%">路径</th>
                        </tr>
                        <?php echo $dataModel->getScriptStatusRows(); ?>
                    </table>
                </div>
            </div>

            <div class="tab">
                <input type="radio" id="tab-visualise" name="tab-group-1">
                <label for="tab-visualise">可视化分区</label>
                <div class="content"></div>
            </div>

        </div>

        <div id="graph">
            <form>
                <label><input type="radio" name="dataset" value="memory" checked> 内存</label>
                <label><input type="radio" name="dataset" value="keys"> 哈希</label>
                <label><input type="radio" name="dataset" value="hits"> 命中</label>
                <label><input type="radio" name="dataset" value="restarts"> 重新启动</label>
                <label><a href="?opcache_reset=true" onclick="return confirm('确定要重置操作码缓存?')">重置</a> </label>
            </form>

            <div id="stats"></div>
        </div>
    </div>

    <div id="close-partition">&#10006; 关闭可视化</div>
    <div id="partition"></div>

    <script>
        var dataset = <?php echo $dataModel->getGraphDataSetJson(); ?>;

        var width = 400,
            height = 400,
            radius = Math.min(width, height) / 2,
            colours = ['#B41F1F', '#1FB437', '#ff7f0e'];

        d3.scale.customColours = function() {
            return d3.scale.ordinal().range(colours);
        };

        var colour = d3.scale.customColours();
        var pie = d3.layout.pie().sort(null);

        var arc = d3.svg.arc().innerRadius(radius - 20).outerRadius(radius - 50);
        var svg = d3.select("#graph").append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .append("g")
                    .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        var path = svg.selectAll("path")
                      .data(pie(dataset.memory))
                      .enter().append("path")
                      .attr("fill", function(d, i) { return colour(i); })
                      .attr("d", arc)
                      .each(function(d) { this._current = d; }); // store the initial values

        d3.selectAll("input").on("change", change);
        set_text("memory");

        function set_text(t) {
            if (t === "memory") {
                d3.select("#stats").html(
                    "<table><tr><th style='background:#B41F1F;'>已用</th><td><?php echo $dataModel->getHumanUsedMemory()?></td></tr>"+
                    "<tr><th style='background:#1FB437;'>空闲</th><td><?php echo $dataModel->getHumanFreeMemory()?></td></tr>"+
                    "<tr><th style='background:#ff7f0e;' rowspan=\"2\">浪费</th><td><?php echo $dataModel->getHumanWastedMemory()?></td></tr>"+
                    "<tr><td><?php echo $dataModel->getWastedMemoryPercentage()?>%</td></tr></table>"
                );
            } else if (t === "keys") {
                d3.select("#stats").html(
                    "<table><tr><th style='background:#B41F1F;'>缓存哈希数量</th><td>"+format_value(dataset[t][0])+"</td></tr>"+
                    "<tr><th style='background:#1FB437;'>可用哈希数量</th><td>"+format_value(dataset[t][1])+"</td></tr></table>"
                );
            } else if (t === "hits") {
                d3.select("#stats").html(
                    "<table><tr><th style='background:#B41F1F;'>未命中</th><td>"+format_value(dataset[t][0])+"</td></tr>"+
                    "<tr><th style='background:#1FB437;'>命中</th><td>"+format_value(dataset[t][1])+"</td></tr></table>"
                );
            } else if (t === "restarts") {
                d3.select("#stats").html(
                    "<table><tr><th style='background:#B41F1F;'>因为内存</th><td>"+dataset[t][0]+"</td></tr>"+
                    "<tr><th style='background:#1FB437;'>因为手动</th><td>"+dataset[t][1]+"</td></tr>"+
                    "<tr><th style='background:#ff7f0e;'>因为哈希</th><td>"+dataset[t][2]+"</td></tr></table>"
                );
            }
        }

        function change() {
            // Filter out any zero values to see if there is anything left
            var remove_zero_values = dataset[this.value].filter(function(value) {
                return value > 0;
            });

            // Skip if the value is undefined for some reason
            if (typeof dataset[this.value] !== 'undefined' && remove_zero_values.length > 0) {
                $('#graph').find('> svg').show();
                path = path.data(pie(dataset[this.value])); // update the data
                path.transition().duration(750).attrTween("d", arcTween); // redraw the arcs
            // Hide the graph if we can't draw it correctly, not ideal but this works
            } else {
                $('#graph').find('> svg').hide();
            }

            set_text(this.value);
        }

        function arcTween(a) {
            var i = d3.interpolate(this._current, a);
            this._current = i(0);
            return function(t) {
                return arc(i(t));
            };
        }

        function size_for_humans(bytes) {
            if (bytes > 1048576) {
                return (bytes/1048576).toFixed(2) + ' MB';
            } else if (bytes > 1024) {
                return (bytes/1024).toFixed(2) + ' KB';
            } else return bytes + ' bytes';
        }

        function format_value(value) {
            if (dataset["TSEP"] == 1) {
                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else {
                return value;
            }
        }

        var w = window.innerWidth,
            h = window.innerHeight,
            x = d3.scale.linear().range([0, w]),
            y = d3.scale.linear().range([0, h]);

        var vis = d3.select("#partition")
                    .style("width", w + "px")
                    .style("height", h + "px")
                    .append("svg:svg")
                    .attr("width", w)
                    .attr("height", h);

        var partition = d3.layout.partition()
                .value(function(d) { return d.size; });

        root = JSON.parse('<?php echo json_encode($dataModel->getD3Scripts()); ?>');

        var g = vis.selectAll("g")
                   .data(partition.nodes(root))
                   .enter().append("svg:g")
                   .attr("transform", function(d) { return "translate(" + x(d.y) + "," + y(d.x) + ")"; })
                   .on("click", click);

        var kx = w / root.dx,
                ky = h / 1;

        g.append("svg:rect")
         .attr("width", root.dy * kx)
         .attr("height", function(d) { return d.dx * ky; })
         .attr("class", function(d) { return d.children ? "parent" : "child"; });

        g.append("svg:text")
         .attr("transform", transform)
         .attr("dy", ".35em")
         .style("opacity", function(d) { return d.dx * ky > 12 ? 1 : 0; })
         .text(function(d) { return d.name; })

        d3.select(window)
          .on("click", function() { click(root); })

        function click(d) {
            if (!d.children) return;

            kx = (d.y ? w - 40 : w) / (1 - d.y);
            ky = h / d.dx;
            x.domain([d.y, 1]).range([d.y ? 40 : 0, w]);
            y.domain([d.x, d.x + d.dx]);

            var t = g.transition()
                     .duration(d3.event.altKey ? 7500 : 750)
                     .attr("transform", function(d) { return "translate(" + x(d.y) + "," + y(d.x) + ")"; });

            t.select("rect")
             .attr("width", d.dy * kx)
             .attr("height", function(d) { return d.dx * ky; });

            t.select("text")
             .attr("transform", transform)
             .style("opacity", function(d) { return d.dx * ky > 12 ? 1 : 0; });

            d3.event.stopPropagation();
        }

        function transform(d) {
            return "translate(8," + d.dx * ky / 2 + ")";
        }

        $(document).ready(function() {

            function handleVisualisationToggle(close) {

                $('#partition, #close-partition').fadeToggle();

                // Is the visualisation being closed? If so show the status tab again
                if (close) {

                    $('#tab-visualise').removeAttr('checked');
                    $('#tab-status').trigger('click');

                }

            }

            $('label[for="tab-visualise"], #close-partition').on('click', function() {

                handleVisualisationToggle(($(this).attr('id') === 'close-partition'));

            });

            $(document).keyup(function(e) {

                if (e.keyCode == 27) handleVisualisationToggle(true);

            });

        });
    </script>
</body>
</html>
