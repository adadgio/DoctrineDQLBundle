/**
 * HttpInput.js
 *
 * Utility for creating server side http compatible complex queries
 * to use with repositories and Adadgio\DoctrineDQLBundle repository helpers.
 * @author Romain Bruckert {@link https://github.com/adadgio}
 * @package Adadgio\HttpQueryBundle {@link }
 */
var HttpQuery = {
    sort: false,
    offset: 0,
    limit: false,
    filter: {},

    /**
     * Clear internals.
     */
    start: function () {
        return this.clear();
    },

    /**
     * Clear internals.
     */
    clear: function () {
        this.sort = false;
        this.offset = 0;
        this.limit = false;
        this.filter = {};
        return this;
    },

    /**
     * Set a limit.
     *
     * @param integer Limit value
     * @return \this
     */
    setLimit: function (limit) {
        this.limit = parseInt(limit);
        return this;
    },

    /**
     * Set an offset.
     *
     * @param integer Offset value
     * @return \this
     */
    setOffset: function (offset) {
        this.offset = parseInt(offset);
        return this;
    },

    /**
     * Set a limit.
     *
     * @param integer Limit value
     * @return \this
     */
    setSort: function (field, dir) {
        this.sort = field + ':' + dir;
        return this;
    },

    /**
     * Add filter to the query.
     *
     * @param
     * @param
     */
    andWhere: function (field, value) {
        this.filter[field] = value;

        return this;
    },

    /**
     * Get final query string.
     *
     * @return string HTTP query string uri
     */
    buildQuery: function () {

    },
    
    /**
     * Get final query string.
     *
     * @return string HTTP query string uri
     */
    getQuery: function () {
        var query = [],
            post = this.getPostQuery();

        if (post.limit !== false) {
            query.push('limit=' + post.limit);
        }

        if (post.offset > 0) {
            query.push('offset=' + post.offset);
        }

        if (post.sort) {
            query.push('sort=' + post.sort);
        }

        var f = [];
        for (var key in post.filter) { f.push(key + ':' + post.filter[key]); }
        query.push('filter=' + f.join(' AND '));

        // return query;
        return encodeURI(query.join('&'));
    },

    /**
     * Get final query string.
     *
     * @return string HTTP query string uri
     */
    getPostQuery: function () {
        return {
            sort: this.sort,
            offset: this.offset,
            limit: this.limit,
            filter: this.filter,
        };
    },

    /**
     * Custom filter.
     *
     * @param string Field name
     * @param mixed  Filter value
     */
    andLike: function (field, value) {
        this.filter[field+'($LIKE)'] = value;
        return this;
    },

    // @todo
    andLlike: function (field, value) {

    },

    // @todo
    andRlike: function (field, value) {

    },

    // @todo
    andIsNull: function (field) {

    },

    // @todo
    andIsNotNull: function (field) {

    },

    /**
     * Custom filter.
     *
     * @param string Field name
     * @param mixed  Filter value
     */
    andBetween: function (field, values) {
        values = values.map(function (v) { return (typeof(v) === 'string') ? "'"+ v +"'" : v; });

        this.filter[field+'($BETWEEN)'] = '[' + values.join(',') + ']';
        return this;
    },

    /**
     * Custom filter.
     *
     * @param string Field name
     * @param mixed  Filter value
     */
    andIn: function (field, values) {
        values = values.map(function (v) { return (typeof(v) === 'string') ? "'v'" : v; });

        this.filter[field+'($IN)'] = '[' + values.join(',') + ']';
        return this;
    },

    /**
     * Custom filter.
     *
     * @param string Field name
     * @param mixed  Filter value
     */
    andNotIn: function (field, values) {
        values = values.map(function (v) { return (typeof(v) === 'string') ? "'v'" : v; });

        this.filter[field+'($NOT IN)'] = '[' + values.join(',') + ']';
        return this;
    },
};
