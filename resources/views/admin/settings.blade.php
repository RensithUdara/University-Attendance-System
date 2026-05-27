@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section animate-fade-in">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="welcome-title">System Settings ⚙️</h2>
                <p class="welcome-subtitle">Manage system configuration and preferences</p>
            </div>
            <div class="col-auto">
                <div class="current-time">
                    <i class="fas fa-cog me-2"></i>
                    <span id="currentTime">{{ now()->format('l, F j, Y - h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dashboard-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs me-2"></i>System Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        System settings page is under development. This will contain configuration options for the entire system.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="settings-section">
                                <h5>General Settings</h5>
                                <p class="text-muted">System-wide configuration options</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="settings-section">
                                <h5>Security Settings</h5>
                                <p class="text-muted">Security and access control settings</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection