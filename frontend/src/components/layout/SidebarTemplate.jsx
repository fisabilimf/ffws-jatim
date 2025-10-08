import React, { useState, useEffect } from 'react';

const SidebarTemplate  = ({ 
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
      className={`fixed top-20 left-0 h-[calc(100vh-5rem)] w-96 bg-white shadow-2xl z-[60] transform transition-all duration-300 ease-in-out flex flex-col rounded-tr-lg rounded-br-lg ${
        isVisible ? 'translate-x-0 opacity-100' : '-translate-x-full opacity-0'
      }`}
      style={{ willChange: 'transform, opacity' }}
    >
      <div className="rounded-tr-lg p-4 border-b border-gray-200 bg-gray-50/50 flex-shrink-0">
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
                
                {/* --- PERUBAHAN UTAMA DI SINI --- */}
                {showArrow && !isDetailPanelOpen && (
                  <div className="mt-4 pt-4 border-t border-gray-200">
                    <button
                      onClick={onArrowToggle}
                      className="group w-full flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 hover:border-blue-500 hover:bg-blue-50 hover:shadow-lg transition-all duration-300 ease-in-out"
                      title="Buka Detail Panel"
                    >
                      {/* Konten teks di sebelah kiri */}
                      <div className="text-left">
                        <span className="font-semibold text-slate-800 group-hover:text-blue-800 transition-colors">
                          Detail Informasi
                        </span>
                        <p className="text-sm text-slate-500 group-hover:text-blue-600 transition-colors">
                          Lihat data lengkap stasiun
                        </p>
                      </div>

                      {/* Ikon panah di sebelah kanan */}
                      <div className="text-slate-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                          <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                      </div>
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