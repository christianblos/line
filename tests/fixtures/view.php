Hello <?php echo $name;

if (isset($throw)) {
    throw new Exception('from view');
}
