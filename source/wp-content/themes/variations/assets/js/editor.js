/**
 * This file contains JavaScript for the editor
 *
 */

/*
 * Core blocks customization
 */

/** the property sets position absolute for to a group */
wp.blocks.registerBlockStyle('core/group',
    {
        name: 'header-fixed',
        label: 'Fixed Header',
    }
);

/** add padding for a search field */
wp.blocks.registerBlockStyle('core/search',
    {
        name: 'high-search',
        label: 'High Search',
    }
);

/** core/categories modern list */
wp.blocks.registerBlockStyle('core/categories',
    {
        name: 'modern-list',
        label: 'Modern List',
    }
);

/** core/query-pagination-numbers modern list */
wp.blocks.registerBlockStyle('core/query-pagination-numbers',
    {
        name: 'rounded-numbers',
        label: 'Rounded',
    }
);

/** core/post-terms rounded */
wp.blocks.registerBlockStyle('core/post-terms',
    {
        name: 'rounded-terms',
        label: 'Rounded',
    }
);

/** core/post-author blog style */
wp.blocks.registerBlockStyle('core/post-author',
    {
        name: 'blog-style',
        label: 'Blog Style',
    }
);

/** core/post-navigation-link blog style */
wp.blocks.registerBlockStyle('core/post-navigation-link',
    {
        name: 'blog-style',
        label: 'Blog Style',
    }
);

/** core/comments blog style */
wp.blocks.registerBlockStyle('core/comments',
    {
        name: 'blog-style',
        label: 'Blog Style',
    }
);

