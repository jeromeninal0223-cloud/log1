#!/usr/bin/env python3
"""
Startup script for the AI Bid Analysis API Server
"""

import subprocess
import sys
import os

def start_server():
    print("Starting AI Bid Analysis API Server...")
    
    # Change to the correct directory
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    
    try:
        # Start the Flask server
        subprocess.run([sys.executable, "api_server.py"], check=True)
    except KeyboardInterrupt:
        print("\nServer stopped by user")
    except Exception as e:
        print(f"Error starting server: {e}")

if __name__ == "__main__":
    start_server()
