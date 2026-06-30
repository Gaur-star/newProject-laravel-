@extends('layout')

@section('content')
    
    <h1>Monitored Sites</h1>

    <div class="site-list">
        <!-- Site 1 -->
        <div class="site-item" data-site-id="1">
            <div class="site-info">
                <span class="site-name">Production App</span>
                <span class="site-url">https://example.com</span>
            </div>
            <div class="action-area">
                <span class="status-message">✓ Success</span>
                <button class="update-btn" onclick="updateSite(1, this)">Update</button>
            </div>
        </div>

        <!-- Site 2 -->
        <div class="site-item" data-site-id="2">
            <div class="site-info">
                <span class="site-name">Staging Environment</span>
                <span class="site-url">https://example.com</span>
            </div>
            <div class="action-area">
                <span class="status-message">✓ Success</span>
                <button class="update-btn" onclick="updateSite(2, this)">Update</button>
            </div>
        </div>

        <!-- Site 3 -->
        <div class="site-item" data-site-id="3">
            <div class="site-info">
                <span class="site-name">Documentation Wiki</span>
                <span class="site-url">https://example.com</span>
            </div>
            <div class="action-area">
                <span class="status-message">✓ Success</span>
                <button class="update-btn" onclick="updateSite(3, this)">Update</button>
            </div>
        </div>
    </div>



    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #16a34a;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .site-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .site-item {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .site-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .site-name {
            font-weight: 600;
        }

        .site-url {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .action-area {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 140px;
            justify-content: flex-end;
        }

        .update-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .update-btn:hover {
            background-color: var(--primary-hover);
        }

        .update-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .status-message {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--success);
            display: none;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <script>
        function updateSite(siteId, buttonElement) {
            const actionArea = buttonElement.parentElement;
            const successMessage = actionArea.querySelector('.status-message');
            
            // UI Feedback: Disable button and change text during the simulated action
            buttonElement.disabled = true;
            buttonElement.innerText = 'Updating...';
            successMessage.style.display = 'none';

            // Simulate an API network request (1.5-second delay)
            setTimeout(() => {
                // Reset button state
                buttonElement.disabled = false;
                buttonElement.innerText = 'Update';
                
                // Show the success text
                successMessage.style.display = 'inline';

                // Automatically hide the success message after 3 seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000);

            }, 1500);
        }
    </script>


@endsection