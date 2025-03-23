import * as React from "react";
import { ChevronLeft, ChevronRight, MoreHorizontal } from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";

interface PaginationProps {
  currentPage: number;
  totalPages: number;
  onPageChange: (page: number) => void;
  className?: string;
  maxDisplayedPages?: number;
}

export function Pagination({
  currentPage,
  totalPages,
  onPageChange,
  className,
  maxDisplayedPages = 5,
}: PaginationProps) {
  const getPageNumbers = () => {
    // Always show first and last page
    const firstPage = 1;
    const lastPage = totalPages;

    let startPage = Math.max(firstPage, currentPage - Math.floor(maxDisplayedPages / 2));
    let endPage = Math.min(lastPage, startPage + maxDisplayedPages - 1);
    
    // Adjust start page if needed to ensure we show maxDisplayedPages
    if (endPage - startPage + 1 < maxDisplayedPages) {
      startPage = Math.max(firstPage, endPage - maxDisplayedPages + 1);
    }

    const pages: (number | 'ellipsis')[] = [];
    
    // Add first page
    if (startPage > firstPage) {
      pages.push(firstPage);
      if (startPage > firstPage + 1) {
        pages.push('ellipsis');
      }
    }
    
    // Add middle pages
    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }
    
    // Add last page
    if (endPage < lastPage) {
      if (endPage < lastPage - 1) {
        pages.push('ellipsis');
      }
      pages.push(lastPage);
    }
    
    return pages;
  };

  return (
    <nav
      role="navigation"
      aria-label="Pagination Navigation"
      className={cn("flex items-center space-x-1", className)}
    >
      <Button
        variant="outline"
        size="icon"
        onClick={() => onPageChange(currentPage - 1)}
        disabled={currentPage <= 1}
        aria-label="Go to previous page"
      >
        <ChevronLeft className="h-4 w-4" />
      </Button>
      
      {getPageNumbers().map((page, index) => {
        if (page === 'ellipsis') {
          return (
            <div key={`ellipsis-${index}`} className="flex items-center justify-center h-9 w-9">
              <MoreHorizontal className="h-4 w-4" />
            </div>
          );
        }
        
        return (
          <Button
            key={page}
            variant={currentPage === page ? "default" : "outline"}
            size="icon"
            onClick={() => onPageChange(page)}
            aria-label={`Page ${page}`}
            aria-current={currentPage === page ? "page" : undefined}
          >
            {page}
          </Button>
        );
      })}
      
      <Button
        variant="outline"
        size="icon"
        onClick={() => onPageChange(currentPage + 1)}
        disabled={currentPage >= totalPages}
        aria-label="Go to next page"
      >
        <ChevronRight className="h-4 w-4" />
      </Button>
    </nav>
  );
}
