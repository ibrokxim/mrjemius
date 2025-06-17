<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasFactory;
    protected $table = 'seo_metas';
    protected $fillable = [  'model_type',
        'model_id',
        'locale',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image_url',
        'canonical_url',
        'robots_tags',
        'custom_html_head_start',
        'custom_html_head_end',
        'custom_html_body_start',
        'custom_html_body_end',
        ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

}
