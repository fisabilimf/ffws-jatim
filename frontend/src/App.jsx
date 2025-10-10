import React from "react";
import { AppProvider } from "@/contexts/AppContext";
import { DevicesProvider } from "@/contexts/DevicesContext";
import Layout from "@/components/layout/Layout.jsx";
import "@/assets/App.css";

function App() {
    return (
        <AppProvider>
            <DevicesProvider>
                <Layout />
            </DevicesProvider>
        </AppProvider>
    );
}

export default App;
