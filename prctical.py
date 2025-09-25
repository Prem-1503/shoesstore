import tkinter as tk
from tkinter import messagebox

root = tk.Tk()
root.title("Menu Example")
root.geometry("400x300")

# Functions
def new_file():
    win = tk.Toplevel(root)
    win.title("New Window")
    win.geometry("300x200")
    tk.Button(win, text="Return", command=win.destroy).pack(pady=20)

def open_file():
    messagebox.showerror("Error", "Can't Open File")

def save_file():
    messagebox.showinfo("Save", "File Saved")

def exit_app():
    root.destroy()

def cut(): text.event_generate("<<Cut>>")
def copy(): text.event_generate("<<Copy>>")
def paste(): text.event_generate("<<Paste>>")

# Menu
menubar = tk.Menu(root)

file_menu = tk.Menu(menubar, tearoff=0)
file_menu.add_command(label="New", command=new_file)
file_menu.add_command(label="Open", command=open_file)
file_menu.add_command(label="Save", command=save_file)
file_menu.add_separator()
file_menu.add_command(label="Exit", command=exit_app)
menubar.add_cascade(label="File", menu=file_menu)

edit_menu = tk.Menu(menubar, tearoff=0)
edit_menu.add_command(label="Cut", command=cut)
edit_menu.add_command(label="Copy", command=copy)
edit_menu.add_command(label="Paste", command=paste)
menubar.add_cascade(label="Edit", menu=edit_menu)

root.config(menu=menubar)

# Text area
text = tk.Text(root, wrap="word")
text.pack(expand=True, fill="both")

root.mainloop()