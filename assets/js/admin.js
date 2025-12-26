(function($) {
    'use strict';

    var WprCacheStatus = {
        allData: [],
        filteredData: [],
        currentPage: 1,
        perPage: 25,
        rucssEnabled: false,

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $('#wpr-cache-status-refresh').on('click', this.loadStatus.bind(this));
            $('#wpr-cache-status-search').on('input', this.handleSearch.bind(this));
            $('#wpr-cache-status-per-page').on('change', this.handlePerPageChange.bind(this));
            // Auto-load on page load
            this.loadStatus();
        },

        handleSearch: function(e) {
            var query = e.target.value.toLowerCase();
            this.filteredData = this.allData.filter(function(item) {
                return item.url.toLowerCase().indexOf(query) !== -1;
            });
            this.currentPage = 1;
            this.renderTable(this.filteredData);
        },

        handlePerPageChange: function(e) {
            this.perPage = parseInt(e.target.value);
            this.currentPage = 1;
            this.loadStatus();
        },

        loadStatus: function() {
            var self = this;
            var $tbody = $('#wpr-cache-status-body');
            var $container = $('#rocket-warmup-content');

            // Show loading overlay
            $container.addClass('is-loading');

            $tbody.html('<tr><td colspan="4" class="wpr-cache-status-loading">' + 
                wprCacheStatus.strings.loading + '</td></tr>');

            $.ajax({
                url: wprCacheStatus.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rocket_warmup_table_get_status',
                    nonce: wprCacheStatus.nonce
                },
                success: function(response) {
                    // Hide loading overlay
                    $container.removeClass('is-loading');
                    
                    if (response.success && response.data) {
                        self.allData = response.data.items;
                        self.filteredData = response.data.items;
                        self.rucssEnabled = response.data.rucss_enabled;
                        self.currentPage = 1;
                        self.updateStats(response.data.stats);
                        self.updateTableHeaders();
                        self.renderTable(self.filteredData);
                    } else {
                        self.showError();
                    }
                },
                error: function() {
                    // Hide loading overlay
                    $container.removeClass('is-loading');
                    self.showError();
                }
            });
        },

        renderTable: function(data) {
            var $tbody = $('#wpr-cache-status-body');
            var html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="4" class="wpr-cache-status-loading">' + 
                    'No URLs found' + '</td></tr>';
                $tbody.html(html);
                $('#wpr-cache-status-pagination').hide();
                $('#wpr-cache-status-info').text('');
                return;
            }

            // Calculate pagination
            var totalPages = Math.ceil(data.length / this.perPage);
            var start = (this.currentPage - 1) * this.perPage;
            var end = Math.min(start + this.perPage, data.length);
            var pageData = data.slice(start, end);

            // Render rows
            $.each(pageData, function(index, item) {
                html += '<tr>';
                html += '<td><a href="' + item.url + '" target="_blank" class="wpr-cache-status-url">' + 
                    item.url + '</a></td>';
                html += '<td>' + this.getCacheStatusBadge(item.cache_status) + '</td>';
                if (this.rucssEnabled) {
                    html += '<td>' + this.getRucssStatusBadge(item.rucss_status) + '</td>';
                }
                html += '<td>' + (item.last_modified || '-') + '</td>';
                html += '</tr>';
            }.bind(this));

            $tbody.html(html);
            this.updateTableHeaders();

            // Update info
            $('#wpr-cache-status-info').text('Showing ' + (start + 1) + '-' + end + ' of ' + data.length);
            $('#wpr-cache-status-showing').text('Total: ' + this.allData.length + ' URLs' + 
                (this.filteredData.length !== this.allData.length ? ' (filtered: ' + this.filteredData.length + ')' : ''));

            // Render pagination
            this.renderPagination(totalPages);
            $('#wpr-cache-status-pagination').show();
        },

        renderPagination: function(totalPages) {
            var $nav = $('#wpr-cache-status-nav');
            var html = '';

            if (totalPages <= 1) {
                $nav.html('');
                return;
            }

            // Previous button
            if (this.currentPage > 1) {
                html += '<button class="button" data-page="' + (this.currentPage - 1) + '">« Previous</button>';
            }

            // Page numbers
            var startPage = Math.max(1, this.currentPage - 2);
            var endPage = Math.min(totalPages, this.currentPage + 2);

            if (startPage > 1) {
                html += '<button class="button" data-page="1">1</button>';
                if (startPage > 2) {
                    html += '<span style="padding: 6px 12px;">...</span>';
                }
            }

            for (var i = startPage; i <= endPage; i++) {
                if (i === this.currentPage) {
                    html += '<button class="button button-primary" data-page="' + i + '">' + i + '</button>';
                } else {
                    html += '<button class="button" data-page="' + i + '">' + i + '</button>';
                }
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<span style="padding: 6px 12px;">...</span>';
                }
                html += '<button class="button" data-page="' + totalPages + '">' + totalPages + '</button>';
            }

            // Next button
            if (this.currentPage < totalPages) {
                html += '<button class="button" data-page="' + (this.currentPage + 1) + '">Next »</button>';
            }

            $nav.html(html);

            // Bind pagination clicks
            $nav.find('button').on('click', function(e) {
                this.currentPage = parseInt($(e.target).data('page'));
                this.renderTable(this.filteredData);
            }.bind(this));
        },

        updateStats: function(stats) {
            $('#rocket-warmup-cache-percentage').text(stats.cache_percentage);
            $('#rocket-warmup-cache-count').text(stats.cached);
            $('#rocket-warmup-total-count').text(stats.total);
            
            if (this.rucssEnabled) {
                $('#rocket-warmup-rucss-percentage').text(stats.rucss_percentage);
                $('#rocket-warmup-rucss-count').text(stats.rucss_complete);
                $('#rocket-warmup-total-count-rucss').text(stats.total);
                $('#rocket-warmup-rucss-stats').show();
            } else {
                $('#rocket-warmup-rucss-stats').hide();
            }
        },

        updateTableHeaders: function() {
            var colspan = this.rucssEnabled ? 4 : 3;
            
            // Update loading colspan
            $('.wpr-cache-status-loading').attr('colspan', colspan);
        },

        getCacheStatusBadge: function(status) {
            var label = '';
            var className = '';

            if (status === 'cached') {
                label = wprCacheStatus.strings.cached;
                className = 'cached';
            } else {
                label = wprCacheStatus.strings.notCached;
                className = 'not-cached';
            }

            return '<span class="rocket-warmup-status ' + className + '">' + label + '</span>';
        },

        getRucssStatusBadge: function(status) {
            var label = '';
            var className = '';

            switch (status) {
                case 'complete':
                    label = wprCacheStatus.strings.rucssComplete;
                    className = 'rucss-complete';
                    break;
                case 'processing':
                    label = wprCacheStatus.strings.rucssProcessing;
                    className = 'rucss-processing';
                    break;
                case 'failed':
                    label = wprCacheStatus.strings.rucssFailed;
                    className = 'rucss-failed';
                    break;
                case 'disabled':
                    label = wprCacheStatus.strings.rucssDisabled;
                    className = 'rucss-disabled';
                    break;
                default:
                    label = wprCacheStatus.strings.rucssNotStarted;
                    className = 'rucss-not-started';
            }

            return '<span class="rocket-warmup-status ' + className + '">' + label + '</span>';
        },

        showError: function() {
            var $tbody = $('#wpr-cache-status-body');
            $tbody.html('<tr><td colspan="4" class="wpr-cache-status-loading" style="color: #d63638;">' + 
                wprCacheStatus.strings.error + '</td></tr>');
        }
    };

    $(document).ready(function() {
        WprCacheStatus.init();
    });

})(jQuery);
