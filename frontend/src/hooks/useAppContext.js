import { useContext } from "react";
import { AppContext } from "@/contexts/AppContext";

export const useAppContext = () => {
    const context = useContext(AppContext);
    if (context === undefined) {
        throw new Error("useAppContext must be used within an AppProvider");
    }
    return context;
};

// Hook khusus untuk app metadata
export const useAppMetadata = () => {
    const { isOnline, appVersion, lastUpdate, isInitialized } = useAppContext();
    return {
        isOnline,
        appVersion,
        lastUpdate,
        isInitialized
    };
};

// Hook khusus untuk UI preferences
export const useAppPreferences = () => {
    const { theme, language, toggleTheme, changeLanguage } = useAppContext();
    return {
        theme,
        language,
        toggleTheme,
        changeLanguage
    };
};

// Hook khusus untuk app functions
export const useAppFunctions = () => {
    const { checkConnection, updateVersion, initializeApp } = useAppContext();
    return {
        checkConnection,
        updateVersion,
        initializeApp
    };
};

// Hook khusus untuk debug info
export const useAppDebug = () => {
    const { debugInfo } = useAppContext();
    return debugInfo;
};