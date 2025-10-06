export const getStatusColor = (status) => {
    switch (status) {
        case "safe":
            return "text-green-600";
        case "warning":
            return "text-yellow-600";
        case "alert":
            return "text-red-600";
        default:
            return "text-gray-600";
    }
};

export const getStatusBgColor = (status) => {
    switch (status) {
        case "safe":
            return "bg-green-100 border-green-200";
        case "warning":
            return "bg-yellow-100 border-yellow-200";
        case "alert":
            return "bg-red-100 border-red-200";
        default:
            return "bg-gray-100 border-gray-200";
    }
};

export const getStatusDotColor = (status) => {
    switch (status) {
        case "safe":
            return "bg-green-500";
        case "warning":
            return "bg-yellow-500";
        case "alert":
            return "bg-red-500";
        default:
            return "bg-gray-500";
    }
};

export const getStatusText = (status) => {
    switch (status) {
        case "safe":
            return "Aman";
        case "warning":
            return "Waspada";
        case "alert":
            return "Bahaya";
        default:
            return "Tidak Diketahui";
    }
};