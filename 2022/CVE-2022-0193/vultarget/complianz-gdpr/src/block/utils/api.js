import axios from 'axios';

/**
 * Makes a get request to the PostTypes endpoint.
 *
 * @returns {AxiosPromise<any>}
 */
export const getPostTypes = () => axios.get(complianz.site_url+'wp/v2/types');

/**
 * Makes a get request to the desired post type and builds the query string based on an object.
 *
 * @param {string|boolean} restBase - rest base for the query.
 * @param {object} args
 * @returns {AxiosPromise<any>}
 */
export const getDocuments = () => {
    //domain.com/wp-json/complianz/v1/data/doctypes
    return axios.get(complianz.site_url+`complianz/v1/documents`);
};
