import React, { useEffect, useState } from "react";
import AutoSwitchToggle from "@components/common/AutoSwitchToggle";

/**
 * FilterPanel
 * A slide-in panel from the right side, similar to StationDetail but right-anchored.
 * Intended to host filter controls like AutoSwitchToggle, checkboxes, etc.
 */
const FilterPanel = ({
  isOpen,
  onOpen, // tambahkan prop baru untuk membuka panel
  onClose,
  title = "Filter",
  subtitle,
  children,
  widthClass = "w-96", // allow overriding width if needed
  tickerData,
  handleStationChange,
  currentStationIndex,
  handleAutoSwitchToggle
}) => {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    if (isOpen) {
      // delay ensures transition applies when mounted
      const t = setTimeout(() => setIsVisible(true), 10);
      return () => clearTimeout(t);
    } else {
      setIsVisible(false);
    }
  }, [isOpen]);

  // Tombol trigger filter
  // Selalu tampil di kanan atas, di luar panel
  // Panel tetap muncul seperti biasa
  return (
    <>
      <div className="absolute top-4 right-4 z-[80]">
        <button
          onClick={onOpen}
          className="relative inline-flex items-center justify-center w-12 h-12 rounded-full bg-white hover:bg-blue-50 transition-colors"
          title="Buka Filter"
          aria-label="Buka Filter"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="relative z-10 w-6 h-6 text-blue-600 mix-blend-normal pointer-events-none">
            <path d="M22 3H2l8 9v7l4 2v-9l8-9z"></path>
          </svg>
        </button>
      </div>
      {isOpen && (
        <div
          className={`fixed top-20 right-0 h-[calc(100vh-5rem)] ${widthClass} bg-white shadow-2xl z-[70] transform transition-all duration-300 ease-in-out flex flex-col ${
            isVisible ? "translate-x-0 opacity-100" : "translate-x-full opacity-0"
          }`}
          style={{ willChange: "transform, opacity" }}
        >
          {/* Header */}
          <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
            <div className="flex items-start gap-3">
              <div className="flex-1 min-w-0">
                <h3 className="text-lg font-semibold text-gray-900 truncate">{title}</h3>
                {subtitle && <p className="text-gray-500 text-sm truncate">{subtitle}</p>}
              </div>
              <button
                onClick={() => {
                  setIsVisible(false);
                  setTimeout(() => {
                    onClose && onClose();
                  }, 300);
                }}
                className="p-1 hover:bg-gray-100 rounded-full transition-colors"
                title="Tutup"
                aria-label="Tutup panel filter"
              >
                <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          {/* Content */}
          <div className="flex-1 overflow-y-auto overflow-x-hidden p-4">
            <div className="space-y-6">
              <section className="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <h4 className="text-sm font-semibold text-gray-700 mb-3">Auto Switch</h4>
                <div className="border-t border-gray-100 pt-3">
                  <AutoSwitchToggle
                    tickerData={tickerData}
                    onStationChange={handleStationChange}
                    currentStationIndex={currentStationIndex}
                    onAutoSwitchToggle={handleAutoSwitchToggle}
                  />
                </div>
              </section>

              <section className="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <h4 className="text-sm font-semibold text-gray-700 mb-3">Layer Peta</h4>
                <div className="space-y-2 text-sm text-gray-600">
                  <div className="flex items-center justify-between">
                    <span>Elevasi Muka Air</span>
                    <input type="checkbox" className="toggle toggle-sm" />
                  </div>
                  <div className="flex items-center justify-between">
                    <span>Curah Hujan</span>
                    <input type="checkbox" className="toggle toggle-sm" />
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default FilterPanel;
