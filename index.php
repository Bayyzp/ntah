<?php
// Start of PHP - No whitespace or characters before this tag!
// This file handles only the HTML interface
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reverse IP Lookup Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
        }
        .domain-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            transition: all 0.2s;
        }
        .domain-item:hover {
            background-color: #f8f9fa;
        }
        .domain-list {
            max-height: 400px;
            overflow-y: auto;
        }
        #loading {
            display: none;
        }
        .result-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .full-results {
            max-height: 500px;
            overflow: auto;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            display: none;
        }
        #copyResults {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header text-center py-3">
                        <h2><i class="fas fa-search-location"></i> Reverse IP & Domain Lookup</h2>
                        <p class="mb-0">Find all domains hosted on the same IP address</p>
                    </div>
                    <div class="card-body">
                        <form id="lookupForm">
                            <div class="input-group mb-4">
                                <input type="text" class="form-control form-control-lg" id="inputValue" 
                                       placeholder="Enter IP (e.g. 8.8.8.8) or Domain (e.g. google.com)" required>
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <i class="fas fa-search"></i> Lookup
                                </button>
                            </div>
                        </form>

                        <div id="loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Processing your request, please wait...</p>
                        </div>

                        <div id="error" class="alert alert-danger d-none"></div>

                        <div id="results" class="d-none">
                            <div class="result-actions">
                                <div>
                                    <h4 class="mb-0">
                                        Results for IP: <code id="resultIp"></code> 
                                        <span class="badge bg-primary" id="resultCount">0</span>
                                    </h4>
                                    <small class="text-muted" id="resultDomain"></small>
                                </div>
                                <div>
                                    <a id="viewFullResults" href="#" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-expand"></i> View All
                                    </a>
                                    <button id="copyResults" class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                    <a id="downloadResults" href="#" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body p-0">
                                    <div id="domainList" class="domain-list"></div>
                                </div>
                            </div>
                            
                            <div id="fullResults" class="full-results"></div>
                            
                            <div class="mt-3 text-muted small">
                                <i class="fas fa-info-circle"></i> Data provided by <a href="https://otx.alienvault.com" target="_blank">AlienVault OTX</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const form = document.getElementById('lookupForm');
            const input = document.getElementById('inputValue');
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const results = document.getElementById('results');
            const domainList = document.getElementById('domainList');
            const resultCount = document.getElementById('resultCount');
            const resultIp = document.getElementById('resultIp');
            const resultDomain = document.getElementById('resultDomain');
            const downloadResults = document.getElementById('downloadResults');
            const viewFullResults = document.getElementById('viewFullResults');
            const copyResults = document.getElementById('copyResults');
            const fullResults = document.getElementById('fullResults');
            
            // State
            let currentDomains = [];
            let originalInput = '';

            // Helper functions
            function showLoading() {
                loading.style.display = 'block';
                error.classList.add('d-none');
                results.classList.add('d-none');
            }
            
            function hideLoading() {
                loading.style.display = 'none';
            }
            
            function showError(message) {
                error.textContent = message;
                error.classList.remove('d-none');
                hideLoading();
            }

            // Form submission handler
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                originalInput = input.value.trim();
                
                if (!originalInput) {
                    showError('Please enter an IP address or domain name');
                    return;
                }
                
                showLoading();
                
                try {
                    const response = await fetch('api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'input=' + encodeURIComponent(originalInput)
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.error || 'Unknown error occurred');
                    }
                    
                    displayResults(data);
                } catch (err) {
                    showError(err.message || 'An error occurred. Please try again.');
                }
            });

            // Display results function
            function displayResults(data) {
                currentDomains = data.domains;
                resultCount.textContent = data.count;
                resultIp.textContent = data.ip;
                
                // Show original input if it was a domain
                resultDomain.textContent = originalInput !== data.ip 
                    ? `(Resolved from: ${originalInput})` 
                    : '';
                
                // Display domains
                const displayDomains = data.domains.slice(0, 20);
                let html = displayDomains.map(domain => 
                    `<div class="domain-item">${domain}</div>`
                ).join('');
                
                if (data.domains.length > 20) {
                    html += `<div class="domain-item text-center text-muted">
                        + ${data.domains.length - 20} more domains...
                    </div>`;
                }
                
                domainList.innerHTML = html;
                fullResults.textContent = data.domains.join('\n');
                downloadResults.href = data.download_url || '#';
                results.classList.remove('d-none');
                hideLoading();
            }
            
            // Event listeners
            viewFullResults.addEventListener('click', function(e) {
                e.preventDefault();
                const isShowing = fullResults.style.display === 'block';
                fullResults.style.display = isShowing ? 'none' : 'block';
                this.innerHTML = isShowing 
                    ? '<i class="fas fa-expand"></i> View All' 
                    : '<i class="fas fa-compress"></i> Hide All';
            });
            
            copyResults.addEventListener('click', function() {
                if (currentDomains.length === 0) return;
                
                navigator.clipboard.writeText(currentDomains.join('\n')).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy:', err);
                    showError('Failed to copy to clipboard');
                });
            });
        });
    </script>
</body>
</html>