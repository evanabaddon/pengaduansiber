// Filament Sub Navigation Package
// Version: 1.0.0
// Provides dropdown sub-navigation for Filament sidebar items

(function() {
    'use strict';
    
    // Initialize sub-navigation data storage
    window.subNavigationData = window.subNavigationData || {};
    
    // Register sub-navigation data for a specific navigation item
    window.registerSubNavigation = function(id, items) {
        window.subNavigationData[id] = items;
        console.log("📋 Sub-navigation registered for ID:", id, "Items:", items);
    };
    
    // Get exact Filament theme colors based on current mode
    function getFilamentColors() {
        var isDark = document.documentElement.classList.contains("dark");
        return {
            isDark: isDark,
            bg: isDark ? "rgb(40, 42, 45)" : "rgb(255, 255, 255)", // gray-900 : white (matches Filament sidebar)
            border: isDark ? "rgba(255, 255, 255, 0.1)" : "rgb(229, 231, 235)", // white/10 : gray-200 (matches Filament)
            text: isDark ? "rgb(229, 231, 235)" : "rgb(55, 65, 81)", // gray-200 : gray-700
            textMuted: isDark ? "rgb(156, 163, 175)" : "rgb(107, 114, 128)", // gray-400 : gray-500
            hoverBg: isDark ? "rgba(255, 255, 255, 0.05)" : "rgb(249, 250, 251)", // white/5 : gray-50 (matches Filament hover)
            hoverText: isDark ? "rgb(255, 255, 255)" : "rgb(40, 42, 45)", // white : gray-900
            separator: isDark ? "rgba(255, 255, 255, 0.1)" : "rgb(243, 244, 246)", // white/10 : gray-100
            shadowOpacity: isDark ? "0.25" : "0.1"
        };
    }
    
    // Update dropdown styles based on current theme
    function updateDropdownStyles(dropdown, items) {
        var colors = getFilamentColors();
        
        // Apply dropdown container styles
        dropdown.style.position = "absolute";
        dropdown.style.top = "100%";
        dropdown.style.left = "0";
        dropdown.style.right = "0";
        dropdown.style.background = colors.bg;
        dropdown.style.border = "1px solid " + colors.border;
        dropdown.style.borderRadius = "0.5rem";
        dropdown.style.boxShadow = "0 10px 15px -3px rgba(0, 0, 0, " + colors.shadowOpacity + "), 0 4px 6px -2px rgba(0, 0, 0, 0.05)";
        dropdown.style.zIndex = "50";
        dropdown.style.opacity = "0";
        dropdown.style.visibility = "hidden";
        dropdown.style.transform = "translateY(-10px)";
        dropdown.style.transition = "all 0.2s ease";
        dropdown.style.minWidth = "200px";
        dropdown.style.maxWidth = "300px";
        
        // Clear existing content
        dropdown.innerHTML = "";
        
        // Create dropdown items
        items.forEach(function(item, index) {
            var link = document.createElement("a");
            link.href = item.url;
            link.style.display = "flex";
            link.style.alignItems = "center";
            link.style.padding = "0.75rem 1rem";
            link.style.textDecoration = "none";
            link.style.color = colors.text;
            link.style.transition = "all 0.15s ease";
            
            // Add separator border (except for last item)
            if (index < items.length - 1) {
                link.style.borderBottom = "1px solid " + colors.separator;
            }
            
            // Hover effects
            link.addEventListener("mouseenter", function() {
                var currentColors = getFilamentColors();
                link.style.backgroundColor = currentColors.hoverBg;
                link.style.color = currentColors.hoverText;
            });
            
            link.addEventListener("mouseleave", function() {
                var currentColors = getFilamentColors();
                link.style.backgroundColor = "transparent";
                link.style.color = currentColors.text;
            });
            
            // Create content container
            var content = document.createElement("div");
            content.style.flex = "1";
            
            // Create label
            var label = document.createElement("div");
            label.textContent = item.label;
            label.style.fontWeight = "500";
            label.style.fontSize = "0.875rem";
            label.style.lineHeight = "1.25rem";
            content.appendChild(label);
            
            // Create description (if provided)
            if (item.description) {
                var description = document.createElement("div");
                description.textContent = item.description;
                description.style.fontSize = "0.75rem";
                description.style.color = colors.textMuted;
                description.style.marginTop = "0.125rem";
                content.appendChild(description);
            }
            
            link.appendChild(content);
            dropdown.appendChild(link);
        });
    }
    
    // Create a new sub-navigation dropdown
    function createSubNavDropdown(items) {
        var dropdown = document.createElement("div");
        dropdown.className = "sub-nav-dropdown";
        updateDropdownStyles(dropdown, items);
        return dropdown;
    }
    
    // Initialize sub-navigation for all qualifying navigation items
    function initializeSubNavigation() {
        console.log("🚀 === FILAMENT SUB-NAVIGATION INITIALIZATION ===");
        
        var navItems = document.querySelectorAll(".fi-sidebar-item");
        console.log("📱 Found navigation items:", navItems.length);
        console.log("💾 Available sub-navigation data:", window.subNavigationData);
        
        navItems.forEach(function(navItem) {
            var badge = navItem.querySelector(".fi-badge");
            var navLink = navItem.querySelector("a");
            
            // Skip if no badge or no dropdown indicator
            if (!badge || !badge.textContent.includes("▼")) {
                return;
            }
            
            // Skip if already processed
            if (navItem.hasAttribute("data-sub-nav-processed")) {
                return;
            }
            
            // Extract navigation label and generate ID
            var navLabel = navLink ? navLink.textContent.replace(/▼/g, "").replace(/\s+/g, " ").trim() : "";
            var subNavId = navLabel.toLowerCase().replace(/[^a-z0-9]/g, "");
            
            console.log("🔍 Processing nav item:", navLabel, "→ ID:", subNavId);
            
            // Skip if no sub-navigation data found
            if (!window.subNavigationData[subNavId]) {
                console.log("⚠️ No sub-navigation data found for:", navLabel);
                return;
            }
            
            // Mark as processed and prepare for dropdown
            navItem.setAttribute("data-sub-nav-processed", "true");
            navItem.style.position = "relative";
            
            // Create dropdown if it doesn't exist
            var dropdown = navItem.querySelector(".sub-nav-dropdown");
            if (!dropdown) {
                dropdown = createSubNavDropdown(window.subNavigationData[subNavId]);
                navItem.appendChild(dropdown);
                console.log("✅ Created dropdown for:", navLabel);
            }
            
            // Setup hover interactions
            var hoverTimeout;
            
            var showDropdown = function() {
                clearTimeout(hoverTimeout);
                dropdown.style.opacity = "1";
                dropdown.style.visibility = "visible";
                dropdown.style.transform = "translateY(0)";
                console.log("🟢 Showing dropdown:", navLabel);
            };
            
            var hideDropdown = function() {
                hoverTimeout = setTimeout(function() {
                    dropdown.style.opacity = "0";
                    dropdown.style.visibility = "hidden";
                    dropdown.style.transform = "translateY(-10px)";
                    console.log("🔴 Hiding dropdown:", navLabel);
                }, 100);
            };
            
            // Attach event listeners
            navItem.addEventListener("mouseenter", showDropdown);
            navItem.addEventListener("mouseleave", hideDropdown);
            
            dropdown.addEventListener("mouseenter", function() {
                clearTimeout(hoverTimeout);
            });
            dropdown.addEventListener("mouseleave", hideDropdown);
        });
        
        console.log("🎯 Sub-navigation initialization complete!");
    }
    
    // Update all existing dropdowns when theme changes
    function updateAllDropdownThemes() {
        console.log("🎨 Updating all dropdown themes for new color scheme...");
        
        document.querySelectorAll(".sub-nav-dropdown").forEach(function(dropdown) {
            var navItem = dropdown.parentElement;
            var navLabel = "";
            var navLink = navItem.querySelector("a");
            
            if (navLink) {
                navLabel = navLink.textContent.replace(/▼/g, "").trim();
            }
            
            var subNavId = navLabel.toLowerCase().replace(/[^a-z0-9]/g, "");
            
            if (window.subNavigationData[subNavId]) {
                updateDropdownStyles(dropdown, window.subNavigationData[subNavId]);
                console.log("🔄 Updated theme for:", navLabel);
            }
        });
        
        console.log("✨ Theme update complete!");
    }
    
    // Initialize when DOM is ready
    document.addEventListener("DOMContentLoaded", function() {
        console.log("📄 DOM Content Loaded - Setting up Filament Sub-Navigation...");
        
        // Multiple initialization attempts to handle async loading
        setTimeout(initializeSubNavigation, 100);
        setTimeout(initializeSubNavigation, 500);
        setTimeout(initializeSubNavigation, 1000);
        
        // Livewire integration
        if (typeof Livewire !== "undefined") {
            Livewire.hook("message.processed", function() {
                setTimeout(initializeSubNavigation, 100);
            });
            console.log("🔗 Livewire integration enabled");
        }
        
        // Watch for new navigation items (dynamic content)
        var observer = new MutationObserver(function(mutations) {
            var shouldReinit = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && (
                            (node.classList && node.classList.contains("fi-sidebar-item")) ||
                            (node.querySelector && node.querySelector(".fi-sidebar-item"))
                        )) {
                            shouldReinit = true;
                        }
                    });
                }
            });
            
            if (shouldReinit) {
                console.log("🔄 New navigation items detected, reinitializing...");
                setTimeout(initializeSubNavigation, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Watch for theme changes (dark/light mode)
        var darkModeObserver = new MutationObserver(function() {
            console.log("🎨 Theme change detected!");
            updateAllDropdownThemes();
            setTimeout(initializeSubNavigation, 50);
        });
        
        darkModeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ["class"]
        });
        
        console.log("🎉 Filament Sub-Navigation fully initialized!");
    });
    
    // Expose update function globally for manual theme updates
    window.updateFilamentSubNavThemes = updateAllDropdownThemes;
    
})(); 