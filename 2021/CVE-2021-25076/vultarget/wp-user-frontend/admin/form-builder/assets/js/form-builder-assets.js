/**
 * Returns file paths of vue assets
 */

/* global module, require */
function assets() {
    'use strict';

    const grunt     = require('grunt');
    const fs        = require('fs');
    let paths       = {
        mixins:     ['admin/form-builder/assets/js/jquery-siaf-start.js'],
        components: ['admin/form-builder/assets/js/jquery-siaf-start.js'],
        componentTemplates: []
    };

    // mixins
    const mixinsPath  = './admin/form-builder/assets/js/mixins/';
    let mixins        = fs.readdirSync(mixinsPath);

    mixins.forEach((mixin) => {
        const path = `${mixinsPath}${mixin}`;

        if (grunt.file.isFile(path)) {
            paths.mixins.push(path);
        }
    });

    // components
    const componentPath  = './admin/form-builder/assets/js/components/';
    let components       = fs.readdirSync(componentPath);

    components.forEach((component) => {
        const path = `${componentPath}${component}`;

        if (grunt.file.isDir(path)) {
            paths.components.push(path + '/index.js');
            paths.componentTemplates.push(path + '/template.php');
        }
    });

    paths.mixins.push('admin/form-builder/assets/js/jquery-siaf-end.js');
    paths.components.push('admin/form-builder/assets/js/jquery-siaf-end.js');

    return paths;
}

module.exports = assets();
