import React, { useEffect, useState } from "react";

/**
 * FilterPanel
 * A slide-in panel from the right side, similar to StationDetail but right-anchored.
 * Intended to host filter controls like AutoSwitchToggle, checkboxes, etc.
 */
const FilterPanel = ({
  isOpen,
  onClose,
  title = "Filter",
  subtitle,
  children,
  widthClass = "w-96", // allow overriding width if needed
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

  if (!isOpen) return null;

  const handleClose = () => {
    setIsVisible(false);
    // allow the exit transition to finish before unmount
    setTimeout(() => {
      onClose && onClose();
    }, 300);
  };

  return (
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
            onClick={handleClose}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors"
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
        {children}
      </div>
    </div>
  );
};

export default FilterPanel;
