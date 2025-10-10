import { useContext } from "react";
import { DevicesContext } from "@/contexts/DevicesContext";

export const useDevicesContext = () => {
    const context = useContext(DevicesContext);
    if (context === undefined) {
        throw new Error("useDevicesContext must be used within a DevicesProvider");
    }
    return context;
};

// Hook khusus untuk devices (untuk backward compatibility)
export const useDevices = () => {
    const { devices, devicesLoading, devicesError, lastFetch, refreshDevices, hasDevices } = useDevicesContext();
    return {
        devices,
        loading: devicesLoading,
        error: devicesError,
        lastFetch,
        refreshDevices,
        hasDevices
    };
};