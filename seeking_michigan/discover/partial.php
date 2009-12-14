<?
include('image.php');

class Partial {
  public function basic_view($alias, $display_item, $parent_item, $image_types, $seek_search_params, $search_position, $seek_search_params) {
    $conf = &dmGetCollectionFieldInfo($alias);
    $itnum = $display_item['ptr'];

    $rc = dmGetItemInfo($alias, $itnum, $data);
    $parser = xml_parser_create();
    xml_parse_into_struct($parser, $data, $structure, $index);
    xml_parser_free($parser);

    dmGetImageInfo($alias, $itnum, $filename, $type, $width, $height);

    $filename = substr($filename,strrpos($filename, "/") + 1);
    $file_extension = GetFileExt($filename);
    if(in_array($file_extension,$image_types)) {
      $is_image = true;
      $dimensions = Image::fit_width($width, $height, 640);
      $scaled_width = $dimensions[0];
      $scaled_height = $dimensions[1];
      $scaling_factor = $dimensions[2];
      
      $file_url = "http://seekingmichigan.cdmhost.com/cgi-bin/getimage.exe?CISOROOT=".$alias."&amp;CISOPTR=".$itnum;
      $file_url .= "&amp;DMWIDTH=".$scaled_width."&amp;DMHEIGHT=".$scaled_height."&amp;DMSCALE=".$scaling_factor;
    } else {
      $is_image = false;
      $file_url = "http://seekingmichigan.cdmhost.com/cgi-bin/showfile.exe?CISOROOT=".$alias."&amp;CISOPTR=".$itnum;
      $encoded_file_url = urlencode("http://seekingmichigan.cdmhost.com/cgi-bin/showfile.exe?CISOROOT=".$alias."&amp;CISOPTR=".$itnum);
    }
  
    include('basic_view.php');
  }
}
?>
