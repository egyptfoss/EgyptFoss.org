<?php
class Review extends BaseModel {
  protected $table = 'ef_reviews';

  public function saveReview($data) {
    $this->rateable_id = $data['rateable_id'];
    $this->provider_id = $data['provider_id'];
    $this->reviewer_id = $data['reviewer_id'];
    $this->rate = $data['rate'];
    $this->review = $data['review'];

    $review_record = Review::where('rateable_id', '=', $data['rateable_id'])->where('reviewer_id', '=', $data['reviewer_id'])->first();
    if($review_record == null) {
      $this->created_at = date('Y-m-d H:i:s');
    }
    $this->updated_at = date('Y-m-d H:i:s');
    return $this;
  }

  public function updateAverageRate($service_id) {
    $service_meta = new Postmeta;
    $reviewers_count = Review::where('rateable_id', '=', $service_id)->count();
    $rates_sum = Review::where('rateable_id', '=', $service_id)->sum('rate');
    $new_rate = (float)$rates_sum / (float)$reviewers_count;
    $service_meta->updatePostMeta($service_id, 'reviewers_count', $reviewers_count);
    $service_meta->updatePostMeta($service_id, 'rate', $new_rate);
    return array('reviewers_count' => $reviewers_count, 'rate' => $new_rate);
  }

}
