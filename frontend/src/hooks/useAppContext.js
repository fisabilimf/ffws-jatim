import { useContext } from "react";
import { AppContext } from "@/contexts/AppContext";

export const useAppContext = () => {
    const context = useContext(AppContext);
    if (context === undefined) {
        throw new Error("useAppContext must be used within an AppProvider");
    }
    return context;
};

// Hook khusus untuk devices (untuk backward compatibility)
export const useDevices = () => {
    const { devices, devicesLoading, devicesError, lastFetch, refreshDevices, hasDevices } = useAppContext();
    return {
        devices,
        loading: devicesLoading,
        error: devicesError,
        lastFetch,
        refreshDevices,
        hasDevices
    };
};