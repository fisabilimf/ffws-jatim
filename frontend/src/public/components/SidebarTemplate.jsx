import React, { useState, useEffect } from 'react';

const SidebarTemplate = ({ 
  isOpen, 
  onClose, 
  title, 
  subtitle, 
  children,
  headerContent,
  showArrow = false,
  onArrowToggle,
  isDetailPanelOpen = false,
  onCloseDetailPanel
}) => {
  const [isVisible, setIsVisible] = useState(false);
  
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => setIsVisible(true), 10);
    } else {
      setIsVisible(false);
    }
  }, [isOpen]);
  
  const handleClose = () => {
    setIsVisible(false);
    if (isDetailPanelOpen && onCloseDetailPanel) {
      onCloseDetailPanel();
    }
    setTimeout(onClose, 300);
  };
  
  if (!isOpen) return null;
  
  return (
    <div 
      className={`fixed top-20 left-0 h-[calc(100vh-5rem)] w-96 bg-white shadow-2xl z-[60] transform transition-all duration-300 ease-in-out flex flex-col ${
        isVisible ? 'translate-x-0 opacity-100' : '-translate-x-full opacity-0'
      }`}
      style={{ willChange: 'transform, opacity' }}
    >
      <div className="bg-white border-b border-gray-200 p-4 flex-shrink-0">
        <div className="flex items-center space-x-3">
          <button
            onClick={handleClose}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors self-start mt-1"
          >
            <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <div className="flex-1">
            {headerContent || (
              <>
                <h3 className="text-lg font-semibold text-gray-900">{title}</h3>
                {subtitle && <p className="text-gray-500 text-sm">{subtitle}</p>}
                {showArrow && !isDetailPanelOpen && (
                   <div className="mt-3 pt-3 border-t border-gray-100">
                       <button
                         onClick={onArrowToggle}
                         className="w-full inline-flex items-center justify-start gap-2 px-0 py-2.5 bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 text-sm font-medium rounded-lg border border-blue-200 hover:from-blue-100 hover:to-blue-200 hover:shadow-md hover:scale-[1.02] transition-all duration-200"
                         title="Buka Detail Panel"
                       >
                         <span>Detail Informasi Stasiun</span>
                       </button>
                   </div>
                )}
              </>
            )}
          </div>
        </div>
      </div>
      
      <div className="flex-1 overflow-y-auto overflow-x-hidden">
        {children}
      </div>
    </div>
  );
};

export default SidebarTemplate;

