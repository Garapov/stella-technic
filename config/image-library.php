<?php

return [
    /**
     * The models used by the image library.
     */
    "models" => [
        "image" => \Outerweb\ImageLibrary\Models\Image::class,
        "image_conversion" =>
            \Outerweb\ImageLibrary\Models\ImageConversion::class,
    ],

    /**
     * The image driver to use.
     * Supported: "gd", "imagick"
     */
    "image_driver" => "gd",

    /**
     * The maximum file size for images.
     */
    "max_file_size" => "25MB",

    /**
     * The delay to use for the responsive sizes script
     * that runs in the scripts blade component. This
     * is because the responsive sizes initialization
     * sometimes runs before the images is visible
     * (e.g. in a modal that gets opened).
     */
    "blade_script_init_delay" => 300,

    /**
     * The responsive variants options:
     * - min_width: The minimum width for the responsive variants.
     * - min_height: The minimum height for the responsive variants.
     * - factor: The factor to make each responsive iteration smaller.
     */
    "responsive_variants" => [
        "min_width" => 200,
        "min_height" => 200,
        "factor" => 0.5,
    ],

    /**
     * The default disk to use for images.
     */
    "default_disk" => config("filesystems.default"),

    /**
     * Whether to use the spatie translatable
     * for title and alt attributes.
     */
    "spatie_translatable" => false,

    /**
     * The support options:
     * - webp: Whether or not to generate webp images.
     * - responsive_variants: Whether or not to generate responsive variants.
     * - mime_types: The supported mime types.
     */
    "support" => [
        "webp" => false, // Temporarily disable WebP until we have Imagick
        "responsive_variants" => false,
        "mime_types" => ["image/jpeg", "image/png", "image/gif", "image/webp"],
    ],
];
