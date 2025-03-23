// Adapted from: https://ui.shadcn.com/docs/components/toast
import { useState, useEffect, useCallback } from 'react';

export interface Toast {
  id?: string;
  title?: string;
  description?: string;
  action?: React.ReactNode;
  variant?: 'default' | 'destructive' | 'success';
  duration?: number;
}

export type ToasterToast = Toast & {
  id: string;
  open: boolean;
};

export function useToast() {
  const [toasts, setToasts] = useState<ToasterToast[]>([]);

  const addToast = useCallback((toast: Toast) => {
    const id = toast.id || String(Date.now());
    const duration = toast.duration || 5000;

    setToasts((prevToasts) => [
      ...prevToasts,
      { ...toast, id, open: true },
    ]);

    if (duration > 0) {
      setTimeout(() => {
        dismissToast(id);
      }, duration);
    }

    return id;
  }, []);

  const dismissToast = useCallback((id: string) => {
    setToasts((prevToasts) =>
      prevToasts.map((toast) =>
        toast.id === id ? { ...toast, open: false } : toast
      )
    );

    // Remove toast after animation
    setTimeout(() => {
      setToasts((prevToasts) => prevToasts.filter((toast) => toast.id !== id));
    }, 300);
  }, []);

  const toast = useCallback(
    (props: Omit<Toast, 'id'>) => addToast({ ...props }),
    [addToast]
  );

  return { toast, toasts, dismissToast };
}
