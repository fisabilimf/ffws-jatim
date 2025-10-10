import React, { createContext, useState, useEffect, useCallback } from "react";

export const AppContext = createContext();

export const AppProvider = ({ children }) => {
    // Global app state
    const [isOnline, setIsOnline] = useState(navigator.onLine);
    const [appVersion, setAppVersion] = useState('1.0.0');
    const [theme, setTheme] = useState('light');
    const [language, setLanguage] = useState('id');
    const [lastUpdate, setLastUpdate] = useState(null);
    const [isInitialized, setIsInitialized] = useState(false);
    
    // Global app functions
    const checkConnection = useCallback(() => {
        const online = navigator.onLine;
        setIsOnline(online);
        console.log('Connection status:', online ? 'Online' : 'Offline');
        return online;
    }, []);
    
    const updateVersion = useCallback((version) => {
        setAppVersion(version);
        setLastUpdate(Date.now());
        console.log('App version updated to:', version);
    }, []);
    
    const toggleTheme = useCallback(() => {
        setTheme(prev => {
            const newTheme = prev === 'light' ? 'dark' : 'light';
            console.log('Theme changed to:', newTheme);
            return newTheme;
        });
    }, []);
    
    const changeLanguage = useCallback((lang) => {
        setLanguage(lang);
        console.log('Language changed to:', lang);
    }, []);
    
    const initializeApp = useCallback(() => {
        console.log('=== APP INITIALIZATION ===');
        console.log('App version:', appVersion);
        console.log('Theme:', theme);
        console.log('Language:', language);
        console.log('Connection status:', isOnline ? 'Online' : 'Offline');
        
        setIsInitialized(true);
        setLastUpdate(Date.now());
        console.log('App initialized successfully');
        console.log('=== END APP INITIALIZATION ===');
    }, [appVersion, theme, language, isOnline]);
    
    // Monitor connection status
    useEffect(() => {
        const handleOnline = () => {
            console.log('Connection restored');
            setIsOnline(true);
        };
        
        const handleOffline = () => {
            console.log('Connection lost');
            setIsOnline(false);
        };
        
        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);
        
        return () => {
            window.removeEventListener('online', handleOnline);
            window.removeEventListener('offline', handleOffline);
        };
    }, []);
    
    // Initialize app on mount
    useEffect(() => {
        initializeApp();
    }, [initializeApp]);
    
    // Auto-save theme to localStorage
    useEffect(() => {
        localStorage.setItem('ffws-theme', theme);
    }, [theme]);
    
    // Auto-save language to localStorage
    useEffect(() => {
        localStorage.setItem('ffws-language', language);
    }, [language]);
    
    // Load saved preferences on mount
    useEffect(() => {
        const savedTheme = localStorage.getItem('ffws-theme');
        const savedLanguage = localStorage.getItem('ffws-language');
        
        if (savedTheme) {
            setTheme(savedTheme);
        }
        
        if (savedLanguage) {
            setLanguage(savedLanguage);
        }
    }, []);
    
    const value = {
        // App metadata
        isOnline,
        appVersion,
        lastUpdate,
        isInitialized,
        
        // UI preferences
        theme,
        language,
        
        // App functions
        checkConnection,
        updateVersion,
        toggleTheme,
        changeLanguage,
        initializeApp,
        
        // Debug info
        debugInfo: {
            isOnline,
            appVersion,
            theme,
            language,
            lastUpdate,
            isInitialized,
            userAgent: navigator.userAgent,
            platform: navigator.platform,
            language: navigator.language
        }
    };
    
    return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
};