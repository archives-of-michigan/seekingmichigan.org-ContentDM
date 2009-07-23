<?
class Image {
  public function fit_width($image_width, $image_height, $crop_width) {
    if($image_width <= $crop_width) {
      return array($image_width, $image_height, 100);
    } else {
      $scaling = round(($crop_width / $image_width) * 100);
      $scaled_width = $crop_width;
      $scaled_height = round($scaling * $image_height);
      return array($scaled_width, $scaled_height, $scaling);
    }
  }
}
?>